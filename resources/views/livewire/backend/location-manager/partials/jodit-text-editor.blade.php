<div wire:ignore>
    <textarea wire:model.debounce.500ms="content" class="jodit-editor" id="editor-{{ $this->id }}"></textarea>

    <script>
        document.addEventListener('livewire:init', () => {
            const textarea = document.getElementById('editor-{{ $this->id }}');
            if (textarea && !textarea.jodit) {
                const editor = Jodit.make(textarea, {
                    buttons: @json($buttons),
                    height: {{ $height }},
                    uploader: {
                        insertImageAsBase64URI: true, // Optional: Bilder als Base64 einfügen
                    },
                });

                // Synchronisiere Änderungen mit Livewire
                editor.events.on('change', () => {
                    @this.set('content', editor.getEditorValue());
                });
            }
        });
    </script>
</div>
