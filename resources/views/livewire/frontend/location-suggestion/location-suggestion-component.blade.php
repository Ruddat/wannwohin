<div>
    <!-- Vorschlag-Button -->
    <div class="custom-suggestion">
        <a href="#" class="text-decoration-none" wire:click.prevent="openOverlay">
            <span class="custom-nav-button text-color-dark">
                <i class="icon-cloud-download icons text-color-primary"></i> Neuen Ort vorschlagen
            </span>
        </a>
    </div>

    <!-- Overlay -->
    @if($isOpen)
        <div class="overlay">
            <div class="overlay-content">
                <button type="button" class="btn-close" wire:click="closeOverlay">&times;</button>
                <h4>Neuen Ort vorschlagen</h4>
                <form wire:submit.prevent="submitSuggestion">
                    <div class="mb-3">
                        <label for="location" class="form-label">Name des Ortes</label>
                        <input type="text" id="location" class="form-control" wire:model.defer="location">
                    </div>
                    <button type="submit" class="btn btn-primary">Vorschlagen</button>
                    <button type="button" class="btn btn-secondary" wire:click="closeOverlay">Abbrechen</button>
                </form>
            </div>
        </div>
    @endif

<style>
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1050; /* Über allen anderen Elementen */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .overlay-content {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        width: 70%;
        max-width: 600px; /* Maximale Breite */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1051; /* Sicherstellen, dass es über dem Overlay ist */
        text-align: center;
    }

    .btn-close {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
</style>
</div>
