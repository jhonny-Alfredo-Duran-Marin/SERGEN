<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Categoria;
use App\Models\Medida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

use App\Jobs\ProcessItemImages;
use App\Models\Area;
use App\Models\Movimiento;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:items.view'])->only(['index', 'show']);
        $this->middleware(['permission:items.create'])->only(['create', 'store']);
        $this->middleware(['permission:items.update'])->only(['edit', 'update']);
        $this->middleware(['permission:items.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q           = $request->string('q')->toString();
        $categoriaId = $request->integer('categoria_id');
        $areaId      = $request->integer('area_id');
        $tipo        = $request->string('tipo')->toString();
        $estado      = $request->string('estado')->toString();

        $query = Item::query()->with(['area:id,descripcion', 'categoria:id,descripcion', 'medida:id,simbolo']);

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('codigo', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhere('fabricante', 'like', "%{$q}%");
            });
        }
        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }
        if ($areaId) {
            $query->where('area_id', $areaId);
        }
        if (in_array($tipo, ['Herramienta', 'Material'], true)) {
            $query->where('tipo', $tipo);
        }
        if (in_array($estado, ['Activo', 'Pasivo'], true)) {
            $query->where('estado', $estado);
        }

        // Calcular estadísticas ANTES de paginar (sobre la query filtrada)
        $statsQuery = clone $query;

        $total = $statsQuery->count();
        $activos = (clone $query)->where('estado', 'Activo')->count();
        $pasivos = (clone $query)->where('estado', 'Pasivo')->count();

        // Stock bajo: umbral de 3 unidades
        $lowStockThreshold = 3;
        $bajoStock = (clone $query)->where('cantidad', '<=', $lowStockThreshold)->count();

        // Ahora sí, paginar
        $items = $query->orderBy('descripcion')->paginate(20)->withQueryString();

        $categorias = Categoria::orderBy('descripcion')->get(['id', 'descripcion']);
        $areas = Area::orderBy('descripcion')->get(['id', 'descripcion']);

        return view('items.index', compact(
            'items',
            'categorias',
            'areas',
            'total',
            'activos',
            'pasivos',
            'bajoStock',
            'lowStockThreshold'
        ));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('descripcion')->get(['id', 'descripcion']);
        $medidas    = Medida::orderBy('descripcion')->get(['id', 'descripcion', 'simbolo']);
        $areas      = Area::with('sucursal')->get();

        // ------------------------
        // GENERAR CÓDIGO AUTOMÁTICO
        // ------------------------
        $ultimo = Item::where('codigo', 'like', 'ITEM-%')
            ->orderBy('id', 'desc')
            ->value('codigo');

        if ($ultimo) {
            // extraer número final
            $num = intval(str_replace('ITEM-', '', $ultimo)) + 1;
            $codigoAutogenerado = 'ITEM-' . str_pad($num, 3, '0', STR_PAD_LEFT);
        } else {
            $codigoAutogenerado = 'ITEM-001';
        }

        return view('items.create', compact('categorias', 'medidas', 'areas', 'codigoAutogenerado'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id'   => ['required', 'exists:categorias,id'],
            'medida_id'      => ['required', 'exists:medidas,id'],
            'area_id'        => ['required', 'exists:areas,id'],
            'codigo'         => ['required', 'string', 'max:50', 'unique:items,codigo'],
            'descripcion'    => ['required', 'string', 'max:255'],
            'fabricante'     => ['nullable', 'string', 'max:150'],
            'cantidad'       => ['required', 'integer', 'min:0'],
            'piezas'         => ['nullable', 'integer', 'min:0'],
            'costo_unitario' => ['required', 'numeric', 'min:0', 'max:99999999999.99'],
            'estado'         => ['required', Rule::in(['Activo', 'Pasivo', 'Disponible', 'Prestado', 'Dotado', 'Observacion', 'Baja', 'Reservado'])],
            'tipo'           => ['required', Rule::in(['Herramienta', 'Material'])],
            'ubicacion'      => ['nullable', 'string', 'max:150'],
            'fecha_registro' => ['nullable', 'date'],
            'imagen'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5048'],
        ]);

        // Crear primero el item (sin imagen u opcionalmente con thumb si se genera)
        $item = new Item($data);

        // Si hay imagen, generamos THUMB síncrona y encolamos proceso de original
        if ($request->hasFile('imagen')) {
            $manager = new ImageManager(new Driver());

            // base único legible + uuid
            $base = Str::slug($data['codigo'] ?? 'item') . '-' . Str::uuid();

            // 1) Miniatura 300x300 WEBP (rápida para que el index cargue al toque)
            $img = $manager->read($request->file('imagen')->getPathname());
            $thumb = clone $img;
            $thumb->cover(300, 300);
            $thumbPath = 'items/thumbs/' . $base . '.webp';
            Storage::disk('public')->put($thumbPath, (string) $thumb->toWebp(80));
            $item->imagen_thumb = $thumbPath;

            // 2) Guardamos temporal para el Job que optimiza el original (≤1600px)
            $tmpPath = 'items/tmp/' . $base . '.webp';
            Storage::disk('public')->put($tmpPath, (string) $img->toWebp(90));
            // Nota: imagen_path se setea en el Job cuando termine la optimización
        }

        $item->save();

        // Si hubo imagen, encolamos el Job (después del commit)
        if (isset($tmpPath)) {
            ProcessItemImages::dispatch($item, $tmpPath);
        }
        $item->registrarMovimiento("Ingreso", "Registro", $item->cantidad, null, "Registro de Item Nuevo");

        return redirect()->route('items.index')->with('status', 'Item creado.');
    }

    public function show(Item $item)
    {
        $item->load(['categoria:id,descripcion', 'medida:id,descripcion,simbolo', 'area:id,descripcion']);
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categorias = Categoria::orderBy('descripcion')->get(['id', 'descripcion']);
        $medidas = Medida::orderBy('descripcion')->get(['id', 'descripcion', 'simbolo']);
        $areas = Area::with('sucursal')->get();
        return view('items.edit', compact('item', 'categorias', 'medidas', 'areas'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'area_id'        => ['required', 'exists:areas,id'],
            'categoria_id'   => ['required', 'exists:categorias,id'],
            'medida_id'      => ['required', 'exists:medidas,id'],
            'codigo'         => ['required', 'string', 'max:50', Rule::unique('items', 'codigo')->ignore($item->id)],
            'descripcion'    => ['required', 'string', 'max:255'],
            'fabricante'     => ['nullable', 'string', 'max:150'],
            'cantidad'       => ['required', 'integer', 'min:0'],
            'piezas'         => ['nullable', 'integer', 'min:0'],
            'costo_unitario' => ['required', 'numeric', 'min:0', 'max:99999999999.99'],
            'descuento'      => ['required', 'numeric', 'min:0', 'max:99999999999.99'],
            'estado'         => ['required', Rule::in(['Activo', 'Pasivo', 'Disponible', 'Prestado', 'Dotado', 'Observacion', 'Baja', 'Reservado'])],
            'tipo'           => ['required', Rule::in(['Herramienta', 'Material'])],
            'ubicacion'      => ['nullable', 'string', 'max:150'],
            'fecha_registro' => ['nullable', 'date'],
            'imagen'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5048'],
            'remove_imagen'  => ['nullable', 'boolean'],
        ]);
        $copia = $item->replicate();
 
        $oldOriginal = $item->imagen_path;
        $oldThumb    = $item->imagen_thumb;

        // Quitar imagen (ambas) si el usuario lo solicita
        if ($request->boolean('remove_imagen')) {
            if ($oldOriginal) Storage::disk('public')->delete($oldOriginal);
            if ($oldThumb)    Storage::disk('public')->delete($oldThumb);
            $data['imagen_path']  = null;
            $data['imagen_thumb'] = null;
            $oldOriginal = $oldThumb = null;
            $item->registrarMovimiento("Modificacion", "CAMBIO_FOTO", $item->cantidad, null, "remoción de imagen del item");
        }

        // Subir nueva imagen -> generar nueva THUMB síncrona y encolar Job para original
        $tmpPath = null;
        if ($request->hasFile('imagen')) {
            $manager = new ImageManager(new Driver());
            $base = Str::slug($data['codigo'] ?? $item->codigo ?? 'item') . '-' . Str::uuid();

            // 1) Miniatura
            $img = $manager->read($request->file('imagen')->getPathname());
            $thumb = clone $img;
            $thumb->cover(300, 300);
            $thumbPath = 'items/thumbs/' . $base . '.webp';
            Storage::disk('public')->put($thumbPath, (string) $thumb->toWebp(80));
            $data['imagen_thumb'] = $thumbPath;

            // 2) Temporal para el Job
            $tmpPath = 'items/tmp/' . $base . '.webp';
            Storage::disk('public')->put($tmpPath, (string) $img->toWebp(90));
        }

        // Actualiza datos (aquí aún no tenemos el original optimizado si hay tmp)
        $item->update($data);

        // Limpia archivos viejos si subimos nueva imagen
        if ($request->hasFile('imagen')) {
            if ($oldThumb)    Storage::disk('public')->delete($oldThumb);
            if ($oldOriginal) Storage::disk('public')->delete($oldOriginal);
        }

        // Encola procesamiento del original (≤1600px) y setea imagen_path cuando termine
        if ($tmpPath) {
            ProcessItemImages::dispatch($item, $tmpPath);
        }

        if ($item->wasChanged('cantidad')) {
            $item->registrarMovimiento(
                "Modificacion",
                $item->cantidad >  $copia->cantidad ? "AUMENTO_STOCK" : "DESCUENTO_STOCK",
                abs($item->cantidad - $copia->cantidad),
                "Cambio en cantidad"
            );
        }
        if ($item->wasChanged('descripcion')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_DESCRIPCION", $item->cantidad, "Descripción actualizada");
        }
        if ($item->wasChanged('fabricante')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_FABRICANTE", $item->cantidad, "Fabricante actualizado");
        }
        if ($item->wasChanged('piezas')) {
            $item->registrarMovimiento(
                "Modificacion",
                $item->piezas > $copia->piezas ? "AUMENTO_PIEZAS" : "DESCUENTO_PIEZAS",
                abs($item->piezas - $copia->piezas),
                "Cambio en piezas"
            );
        }

        if ($item->wasChanged('costo_unitario')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_COSTO", $item->cantidad, "Antes: {$copia->costo_unitario} — Después: {$item->costo_unitario}");
        }
        if ($item->wasChanged('descuento')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_DESCUENTO", $item->cantidad, "Antes: {$copia->descuento} — Después: {$item->descuento}");
        }
        if ($item->wasChanged('estado')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_ESTADO", $item->cantidad, "Estado actualizado");
        }

        if ($item->wasChanged('ubicacion')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_UBICACION", $item->cantidad, "Nueva ubicación: {$item->ubicacion}");
        }
         if ($item->wasChanged('area_id')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_AREA", $item->cantidad,  "Nueva área: {$item->area->descripcion}");
        }
          if ($item->wasChanged('categoria_id')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_CATEGORIA", $item->cantidad,  "Nueva categoría: {$item->categoria->descripcion}");
        }
           if ($item->wasChanged('medida_id')) {
            $item->registrarMovimiento("Modificacion", "CAMBIO_MEDIDA", $item->cantidad, "Nueva medida: {$item->medida->descripcion}");
        }

        return redirect()->route('items.index')->with('status', 'Item actualizado.');
    }

    public function destroy(Item $item)
    {
        // Guardamos datos ANTES de eliminar
        $itemId = $item->id;
        $codigo = $item->codigo;

        // Eliminar imágenes
        if ($item->imagen_path) {
            Storage::disk('public')->delete($item->imagen_path);
        }
        if ($item->imagen_thumb) {
            Storage::disk('public')->delete($item->imagen_thumb);
        }

        // Eliminar item
        $item->delete();

        // Registrar movimiento manual SIN usar $item después del delete
        Movimiento::create([
            'item_id'  => $itemId,
            'accion'   => "ELIMINAR_ITEM",
            'cantidad' => null,
            'fecha'    => now(),
            'user_id'  => auth()->id(),
            'nota'     => "Item $codigo eliminado del inventario"
        ]);

        return redirect()
            ->route('items.index')
            ->with('status', 'Item eliminado.');
    }
}
