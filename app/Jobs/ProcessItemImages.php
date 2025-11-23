<?php

namespace App\Jobs;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

// Intervention Image v3
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // o Imagick si usas esa extensión

class ProcessItemImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Item $item;
    public string $tmpPath;

    /**
     * @param Item   $item    Item a actualizar
     * @param string $tmpPath Ruta en disk('public') del archivo temporal WEBP (sin optimizar)
     */
    public function __construct(Item $item, string $tmpPath)
    {
        $this->item    = $item;
        $this->tmpPath = $tmpPath;
    }

    public function handle(): void
    {
        // Si el temporal no existe, no hay nada que hacer
        if (!Storage::disk('public')->exists($this->tmpPath)) return;

        $manager = new ImageManager(new Driver());

        // Cargar binario temporal
        $bin = Storage::disk('public')->get($this->tmpPath);
        $img = $manager->read($bin);

        // Optimizar original a máx. 1600px (mantiene proporción), formato WebP calidad 80
        $orig = clone $img;
        $orig->scaleDown(1600, 1600); // lado mayor <= 1600px

        $base = pathinfo(basename($this->tmpPath), PATHINFO_FILENAME); // mismo nombre base que la miniatura
        $originalPath = 'items/' . $base . '.webp';

        Storage::disk('public')->put($originalPath, (string) $orig->toWebp(80));

        // Guardar en el item
        $this->item->update([
            'imagen_path' => $originalPath,
        ]);

        // Limpiar temporal
        Storage::disk('public')->delete($this->tmpPath);
    }
}
