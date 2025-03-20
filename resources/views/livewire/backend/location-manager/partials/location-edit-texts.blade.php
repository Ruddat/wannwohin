<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Texte bearbeiten -> {{ $locationTitle }}</h3>
        </div>
        <div class="card-body">
            <!-- New: Short Text under Image -->
            <div class="mb-4">
                <label for="imageShortText" class="form-label fw-bold">Kurzer Text unter dem Location Bild max 250 Zeichen</label>
                <livewire:jodit-text-editor
                    wire:model.debounce.500ms="imageShortText"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link']"
                    key="editor-imageShortText-{{ $locationId }}"
                    tabindex="1"
                />
            </div>

            <!-- Gruppe: Panorama Informationen -->
            <div class="mb-4 p-4 border rounded bg-light bg-opacity-10">
                <h5 class="text-success">🌄 Panorama-Informationen</h5>

                <!-- Panorama Titel -->
                <div class="mb-3">
                    <label for="panoramaTitle" class="form-label fw-bold">Panorama-Titel</label>
                    <input type="text" wire:model.live="panoramaTitle" class="form-control border-success" tabindex="2">
                </div>

                <!-- Panorama Kurztext -->
                <div class="mb-3">
                    <label for="panoramaShortText" class="form-label fw-bold">Panorama-Kurztext</label>
                    <livewire:jodit-text-editor
                        wire:model.debounce.500ms="panoramaShortText"
                        :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image', 'unorderedList', 'orderedList']"
                        key="editor-panoramaShortText-{{ $locationId }}"
                        tabindex="3"
                    />
                </div>
            </div>

            <!-- Gruppe: Bildtexte -->
            <div class="mb-4 p-4 border rounded bg-light">
                <h5 class="text-primary">🖼 Bildtexte</h5>

                <!-- Bildtext 1 -->
                <div class="mb-3 d-flex align-items-center">
                    <div class="flex-grow-1">
                        <label for="pic1Text" class="form-label fw-bold">Bildtext (Faktencheck-Bild)</label>
                        <livewire:jodit-text-editor
                            wire:model.debounce.500ms="pic1Text"
                            :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                            key="editor-pic1Text-{{ $locationId }}"
                            tabindex="4"
                        />
                    </div>
                    <div class="ms-3">
                        @if (!empty($textPic1))
                            <img src="{{ $textPic1 }}" alt="Faktencheck" class="img-thumbnail" width="150">
                        @else
                            <img src="{{ asset('img/placeholder.jpg') }}" alt="Kein Bild verfügbar" class="img-thumbnail" width="150">
                        @endif
                    </div>
                </div>

                <!-- Bildtext 2 -->
                <div class="mb-3 d-flex align-items-center">
                    <div class="flex-grow-1">
                        <label for="pic2Text" class="form-label fw-bold">Bildtext (Panorama-Bild optional)</label>
                        <livewire:jodit-text-editor
                            wire:model.debounce.500ms="pic2Text"
                            :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                            key="editor-pic2Text-{{ $locationId }}"
                            tabindex="5"
                        />
                    </div>
                    <div class="ms-3">
                        @if (!empty($textPic2))
                            <img src="{{ $textPic2 }}" alt="Bild 2" class="img-thumbnail" width="150">
                        @else
                            <img src="{{ asset('img/placeholder.jpg') }}" alt="Kein Bild verfügbar" class="img-thumbnail" width="150">
                        @endif
                    </div>
                </div>

                <!-- Bildtext 3 -->
                <div class="mb-3 d-flex align-items-center">
                    <div class="flex-grow-1">
                        <label for="pic3Text" class="form-label fw-bold">Bildtext (Header-Bild optional)</label>
                        <livewire:jodit-text-editor
                            wire:model.debounce.500ms="pic3Text"
                            :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                            key="editor-pic3Text-{{ $locationId }}"
                            tabindex="6"
                        />
                    </div>
                    <div class="ms-3">
                        @if (!empty($textPic3))
                            <img src="{{ $textPic3 }}" alt="Bild 3" class="img-thumbnail" width="150">
                        @else
                            <img src="{{ asset('img/placeholder.jpg') }}" alt="Kein Bild verfügbar" class="img-thumbnail" width="150">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Weitere Texte -->
            <div class="mb-4">
                <label for="textHeadline" class="form-label">Überschrift</label>
                <livewire:jodit-text-editor
                    wire:model.debounce.500ms="textHeadline"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', 'paragraph', '|', 'left', 'center', 'right', 'justify', '|', 'link', 'image']"
                    key="editor-textHeadline-{{ $locationId }}"
                    tabindex="7"
                />
            </div>

            <div class="mb-4">
                <label for="textShort" class="form-label">Kurzer Text</label>
                <livewire:jodit-text-editor
                    wire:model.debounce.500ms="textShort"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image', 'unorderedList', 'orderedList']"
                    key="editor-textShort-{{ $locationId }}"
                    tabindex="8"
                />
            </div>

            <div class="mb-4">
                <label for="textWhatToDo" class="form-label">Was kann man in {{ $locationTitle }} erleben?</label>
                <livewire:jodit-text-editor
                    wire:model.debounce.500ms="textWhatToDo"
                    :buttons="['bold', 'italic', 'underline', '|', 'left', 'center', 'right', 'paragraph', 'fontsize', '|', 'link', 'unorderedList', 'orderedList']"
                    key="editor-textWhatToDo-{{ $locationId }}"
                    tabindex="9"
                />
            </div>

            <div class="mb-4">
                <label for="textLocationClimate" class="form-label">Lage und Klima in {{ $locationTitle }}</label>
                <livewire:jodit-text-editor
                    wire:model.debounce.500ms="textLocationClimate"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'table', '|', 'undo', 'redo']"
                    key="editor-textLocationClimate-{{ $locationId }}"
                    tabindex="10"
                />
            </div>

            <div class="mb-4">
                <label for="panoramaTextAndStyle" class="form-label">Panorama-Text und Stil (optional) {{ $locationTitle }}</label>
                <livewire:jodit-text-editor
                    wire:model.debounce.500ms="panoramaTextAndStyle"
                    :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image', 'table']"
                    key="editor-panoramaTextAndStyle-{{ $locationId }}"
                    tabindex="11"
                />
            </div>
        </div>

        <!-- Footer mit Speichern-Button -->
        <div class="card-footer text-end">
            <button wire:click="save" class="btn btn-primary" tabindex="12">
                <i class="ti ti-check"></i> Speichern
            </button>

            @if (session()->has('success'))
                <div class="alert alert-success mt-3 text-center">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</div>

@assets
    <link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">
    <script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
@endassets

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-toast', ({ type, message }) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: type === 'success' ? '#d4edda' : '#f8d7da',
                    color: '#000',
                });
            });
        });
    </script>
@endpush
