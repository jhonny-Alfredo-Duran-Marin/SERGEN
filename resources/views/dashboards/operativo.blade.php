@extends('adminlte::page')

@section('title', 'Catálogo de Ítems')

@section('content_header')
<h1 class="m-0">
    <i class="fas fa-search-dollar text-success"></i> Buscar y Solicitar Ítem
</h1>
@stop

@section('content')
{{-- BUSCADOR INTERACTIVO --}}
<div class="card card-outline card-success mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-search"></i> Buscador en tiempo real
        </h3>
    </div>
    <div class="card-body">
        <div class="input-group input-group-lg">
            <input type="text" id="buscador" class="form-control" placeholder="Escribe código, descripción, categoría..." autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </div>
        <small class="text-muted">Se buscan mientras escribís</small>
    </div>
</div>

{{-- RESULTADOS --}}
<div id="resultados"></div>

{{-- TARJETA PARA REGISTRAR COMPRA --}}
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exclamation-triangle"></i> ¿No encontrás lo que necesitás?
        </h3>
    </div>
    <div class="card-body text-center">
        <p class="lead">Registrá una solicitud de compra</p>
        <button class="btn btn-warning btn-lg" data-toggle="modal" data-target="#modal-solicitud">
            <i class="fas fa-shopping-cart"></i> SOLICITAR COMPRA
        </button>
    </div>
</div>

{{-- MODAL SOLICITUD COMPRA --}}
<div class="modal fade" id="modal-solicitud">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Solicitar Compra</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form action="{{ route('compras.solicitar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Descripción del producto</label>
                        <input type="text" name="descripcion" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Cantidad aproximada</label>
                        <input type="number" name="cantidad" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label>Urgencia</label>
                        <select name="urgencia" class="form-control">
                            <option value="Normal">Normal</option>
                            <option value="Urgente">Urgente</option>
                            <option value="Crítico">Crítico</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-paper-plane"></i> ENVIAR SOLICITUD
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('buscador').addEventListener('input', function() {
    let term = this.value;
    if (term.length < 2) {
        document.getElementById('resultados').innerHTML = '';
        return;
    }

    fetch(`/api/items/search?q=${term}`)
        .then(r => r.json())
        .then(data => {
            let html = '<div class="row">';
            data.forEach(item => {
                html += `
                <div class="col-md-6 mb-3">
                    <div class="card ${item.cantidad < 5 ? 'border-danger' : ''}">
                        <div class="card-body">
                            <h6 class="card-title"><strong>${item.codigo}</strong> ${item.descripcion}</h6>
                            <p class="card-text">
                                <span class="badge badge-info">${item.categoria}</span>
                                <span class="badge ${item.cantidad < 3 ? 'badge-danger' : (item.cantidad < 10 ? 'badge-warning' : 'badge-success')}">
                                    ${item.cantidad} und
                                </span>
                            </p>
                            ${item.cantidad > 0 ?
                                `<a href="/prestamos/create?item_id=${item.id}" class="btn btn-success btn-sm">
                                    Solicitar Préstamo
                                </a>`
                                : '<span class="text-danger">Agotado</span>'
                            }
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            document.getElementById('resultados').innerHTML = html;
        });
});
</script>
@stop
