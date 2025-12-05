<div class="container-fluid">
    {{-- Breadcrumb start --}}
    <div class="row m-1">
        <div class="col-12">
            <h5>Übersetzungen verwalten</h5>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a href="#" class="f-s-14 f-w-500">
                        <span><i class="ph-duotone ph-translate f-s-16"></i> Übersetzungen</span>
                    </a>
                </li>
                <li class="active">
                    <a href="#" class="f-s-14 f-w-500">Verwalten</a>
                </li>
            </ul>
        </div>
    </div>
    {{-- Breadcrumb end --}}

    {{-- Table section start --}}
    <div class="row table-section">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>Übersetzungen</h5>
                    <p>Verwalte deine Übersetzungen effizient</p>
                </div>

<div class="card-body border-bottom py-3">

    <div class="d-flex flex-wrap gap-3 align-items-center">

        {{-- Per Page --}}
        <div>
            <select wire:model.change="perPage"
                    class="form-select form-select-sm"
                    style="min-width: 80px;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

        {{-- Locale --}}
        <div>
            <select wire:model="selectedLocale"
                    class="form-select form-select-sm"
                    style="min-width: 90px;">
                <option value="">Alle</option>
                @foreach($locales as $locale)
                    <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Search --}}
        <div class="ms-auto position-relative" style="max-width: 250px; width: 100%;">
            <span class="position-absolute top-50 translate-middle-y ms-2 text-secondary">
                <i class="ph-duotone ph-magnifying-glass"></i>
            </span>
            <input
                wire:model.debounce.300ms.live="search"
                type="text"
                class="form-control form-control-sm ps-4"
                placeholder="Suchen..."
            >
        </div>
    </div>

</div>


                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Schlüssel</th>
                                    <th scope="col">Sprache</th>
                                    <th scope="col">Text</th>
                                    <th scope="col" class="text-end">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($translations as $translation)
                                    <tr>
<td class="f-w-500 text-wrap"
    style="white-space: normal; word-break: break-word; max-width: 300px;">
    {{ $translation->key }}
</td>

                                        <td class="text-secondary f-w-600">{{ $translation->locale }}</td>


<td class="f-w-500 text-wrap"
    style="white-space: normal; word-break: break-word; max-width: 300px;">
    {{ $translation->text }}
</td>


                                        <td class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm text-bg-primary border-0"
                                                wire:click="edit({{ $translation->id }})"
                                            >
                                                Bearbeiten
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-sm text-bg-danger border-0"
                                                wire:click="confirmDelete({{ $translation->id }})"
                                            >
                                                Löschen
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Keine Übersetzungen gefunden.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="card-body">
                    {{ $translations->links('vendor.livewire.custom-pagination') }}
                </div>
            </div>
        </div>
    </div>
    {{-- Table section end --}}

    {{-- Edit-Modal --}}
    @if($editingTranslation)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Übersetzung bearbeiten</h5>
                        <button type="button" class="btn-close" wire:click="cancelEdit"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="updateTranslation">
                            <div class="mb-3">
                                <label for="editKey" class="form-label f-w-500">Schlüssel</label>
                                <input
                                    type="text"
                                    id="editKey"
                                    wire:model="editKey"
                                    class="form-control"
                                    readonly
                                >
                                @error('editKey') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="editLocale" class="form-label f-w-500">Sprache</label>
                                <input
                                    type="text"
                                    id="editLocale"
                                    wire:model="editLocale"
                                    class="form-control"
                                    readonly
                                >
                                @error('editLocale') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="editText" class="form-label f-w-500">Text</label>
                                <textarea
                                    id="editText"
                                    wire:model="editText"
                                    class="form-control"
                                    rows="4"
                                ></textarea>
                                @error('editText') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="d-flex justify-content-between">
                                <button
                                    type="button"
                                    class="btn text-bg-secondary border-0"
                                    wire:click="cancelEdit"
                                >
                                    Abbrechen
                                </button>
                                <button
                                    type="submit"
                                    class="btn text-bg-primary border-0"
                                >
                                    Speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete-Confirm-Modal --}}
    @if($confirmingDeleteId)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Übersetzung löschen</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bist du sicher, dass du diese Übersetzung löschen möchtest?</p>
                        <div class="d-flex justify-content-end gap-2">
                            <button
                                type="button"
                                class="btn text-bg-secondary border-0"
                                wire:click="cancelDelete"
                            >
                                Abbrechen
                            </button>
                            <button
                                type="button"
                                class="btn text-bg-danger border-0"
                                wire:click="deleteConfirmed"
                            >
                                Ja, löschen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
