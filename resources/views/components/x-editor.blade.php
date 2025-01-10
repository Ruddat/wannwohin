<div class="form-group">
    @if($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}" wire:model.defer="{{ $name }}" class="form-control x-editor">{{ $value }}</textarea>
</div>

@push('scripts')
    <script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            initializeEditors();

            // Neuinitialisierung nach DOM-Updates
            Livewire.hook('message.processed', (message, component) => {
                initializeEditors();
            });
        });

        function initializeEditors() {
            document.querySelectorAll('.x-editor').forEach(function (editor) {
                if (!editor.classList.contains('ckeditor-initialized')) {
                    const instance = CKEDITOR.replace(editor.id);

                    // Synchronisiere Änderungen mit Livewire
                    instance.on('change', function () {
                        editor.value = instance.getData();
                        editor.dispatchEvent(new Event('input')); // Trigger für Livewire
                    });

                    editor.classList.add('ckeditor-initialized');
                }
            });
        }
    </script>
@endpush
