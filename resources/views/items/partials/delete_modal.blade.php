{{-- resources/views/partials/delete_modal.blade.php --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el item <strong>{{ $item->codigo }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Advertencia:</strong>
                    <ul class="mb-0">
                        <li>Se perderá toda la información del item</li>
                        <li>Esta acción no se puede deshacer</li>
                        @if($item->cantidad > 0)
                            <li class="text-danger">Aún hay <strong>{{ $item->cantidad }}</strong> unidades en inventario</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
