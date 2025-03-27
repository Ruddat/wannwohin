<div>
    @if($showCreateModal)
    <div class="modal fade show d-block" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5>Neuen Standort erstellen</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <!-- Hier dein bisheriges Formular -->
                    @include('livewire.backend.location-manager.partials.location-form-fields')
                </div>
                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn btn-secondary">Abbrechen</button>
                    <button wire:click="save" class="btn btn-primary">Speichern</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
