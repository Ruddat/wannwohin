<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Texte bearbeiten</h3>
        </div>
        <div class="card-body">
            <!-- Feld: Bildtext 1 -->
            <div class="mb-4">
                <label for="pic1Text" class="form-label">Bildtext 1</label>
                <livewire:jodit-text-editor
                    wire:model.live="pic1Text"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                />
            </div>

            <!-- Feld: Bildtext 2 -->
            <div class="mb-4">
                <label for="pic2Text" class="form-label">Bildtext 2</label>
                <livewire:jodit-text-editor
                    wire:model.live="pic2Text"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                />
            </div>

            <!-- Feld: Bildtext 3 -->
            <div class="mb-4">
                <label for="pic3Text" class="form-label">Bildtext 3</label>
                <livewire:jodit-text-editor
                    wire:model.live="pic3Text"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                />
            </div>

            <!-- Feld: Text Headline -->
            <div class="mb-4">
                <label for="textHeadline" class="form-label">Überschrift</label>
                <livewire:jodit-text-editor
                    wire:model.live="textHeadline"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', 'paragraph', '|', 'left', 'center', 'right', 'justify', '|', 'link', 'image']"
                />
            </div>

            <!-- Feld: Text Short -->
            <div class="mb-4">
                <label for="textShort" class="form-label">Kurzer Text</label>
                <livewire:jodit-text-editor
                    wire:model.live="textShort"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image', 'unorderedList', 'orderedList']"
                />
            </div>

            <!-- Feld: Text What To Do -->
            <div class="mb-4">
                <label for="textWhatToDo" class="form-label">Was zu tun ist</label>
                <livewire:jodit-text-editor
                    wire:model.live="textWhatToDo"
                    :buttons="['bold', 'italic', 'underline', '|', 'left', 'center', 'right', 'paragraph', 'fontsize', '|', 'link', 'unorderedList', 'orderedList']"
                />
            </div>

            <!-- Feld: Location Climate -->
            <div class="mb-4">
                <label for="textLocationClimate" class="form-label">Lage und Klima</label>
                <livewire:jodit-text-editor
                    wire:model.live="textLocationClimate"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'table', '|', 'undo', 'redo']"
                />
            </div>

            <!-- Feld: Beste Reisezeit -->
            <div class="mb-4">
                <label for="textBestTravelTime" class="form-label">Beste Reisezeit</label>
                <livewire:jodit-text-editor
                    wire:model.live="textBestTravelTime"
                    :buttons="['bold', 'italic', 'underline', '|', 'left', 'center', 'right', '|', 'unorderedList', 'orderedList', '|', 'link', 'image']"
                />
            </div>

            <!-- Feld: Sports -->
            <div class="mb-4">
                <label for="textSports" class="form-label">Sportmöglichkeiten</label>
                <livewire:jodit-text-editor
                    wire:model.live="textSports"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                />
            </div>

            <!-- Feld: Amusement Parks -->
            <div class="mb-4">
                <label for="textAmusementParks" class="form-label">Freizeitparks</label>
                <livewire:jodit-text-editor
                    wire:model.live="textAmusementParks"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                />
            </div>

            <!-- Feld: Panorama Text -->
            <div class="mb-4">
                <label for="panoramaTextAndStyle" class="form-label">Panorama-Text und Stil</label>
                <livewire:jodit-text-editor
                    wire:model.live="panoramaTextAndStyle"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image', 'table']"
                />
            </div>
        </div>
        <div class="card-footer text-end">
            <button wire:click="save" class="btn btn-primary">
                <i class="ti ti-check"></i> Speichern
            </button>
        </div>
    </div>
</div>

@assets
<!-- Include Jodit CSS Styling -->
<link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">

<!-- Include the Jodit JS Library -->
<script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
@endassets
