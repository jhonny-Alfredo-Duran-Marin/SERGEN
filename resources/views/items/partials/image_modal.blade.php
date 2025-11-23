{{-- resources/views/partials/image_modal.blade.php --}}
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title">
                    <i class="fas fa-image"></i> {{ $item->codigo }} - {{ $item->descripcion }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="{{ $item->imagen_url ?: $item->thumb_url }}"
                     alt="Imagen ampliada"
                     class="img-fluid"
                     style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                @if(!empty($item->imagen_url) && !str_contains($item->imagen_url, 'placeholder'))
                    <a href="{{ $item->imagen_url }}"
                       download
                       class="btn btn-primary">
                        <i class="fas fa-download"></i> Descargar
                    </a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
