<div class="container py-4">

    <h3>Tag Konflikte verwalten</h3>

    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>Raw Category</th>
                <th>Anzahl</th>
                <th>Mapping</th>
                <th>Aktion</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($conflicts as $conflict)
            <tr>
                <td>{{ $conflict->raw_category }}</td>
                <td>{{ $conflict->count }}</td>
                <td>
                    <select wire:model="selectedTag.{{ $conflict->raw_category }}" class="form-select">
                        <option value="">-- Tag auswählen --</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}">
                                {{ $tag->group }} → {{ $tag->title }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button wire:click="saveMapping('{{ $conflict->raw_category }}')"
                            class="btn btn-sm btn-success">
                        Speichern & Auflösen
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $conflicts->links() }}

</div>
