<div class="container">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Quick Filter Manager</h2>
            <p class="card-subtitle">Hier kannst du die Quick-Filter verwalten, bearbeiten und per Drag & Drop neu anordnen.</p>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <!-- Formular nur anzeigen, wenn $showForm true ist -->
            @if ($showForm)
                <form wire:submit.prevent="save">
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Titel:</label>
                        <input type="text" id="title" wire:model="title" class="form-control">
                        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Title Text -->
                    <div class="mb-3">
                        <label for="title_text" class="form-label">Titel-Text:</label>
                        <textarea id="title_text" wire:model="title_text" class="form-control"></textarea>
                        @error('title_text') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Normal Content -->
                    <div class="mb-4">
                        <label for="content" class="form-label">Panorama-Text und Stil</label>
                        <livewire:jodit-text-editor
                            wire:model.live="content"
                            :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image', 'table']"
                        />
                        @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Images Row -->
                    <div class="row">
                        <!-- Thumbnail Upload mit Vorschau -->
                        <div class="col-md-4 mb-3">
                            <label for="thumbnail" class="form-label">Thumbnail:</label>
                            <input type="file" id="thumbnail" wire:model="thumbnail" class="form-control">
                            @if($existingThumbnail)
                                <img src="{{ asset('storage/' . $existingThumbnail) }}" class="img-thumbnail mt-2" width="100" alt="Thumbnail Preview">
                            @endif
                            @error('thumbnail') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Panorama Upload mit Vorschau -->
                        <div class="col-md-4 mb-3">
                            <label for="panorama" class="form-label">Panorama:</label>
                            <input type="file" id="panorama" wire:model="panorama" class="form-control">
                            @if($existingPanorama)
                                <img src="{{ asset('storage/' . $existingPanorama) }}" class="img-thumbnail mt-2" width="100" alt="Panorama Preview">
                            @endif
                            @error('panorama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Bild Upload mit Vorschau -->
                        <div class="col-md-4 mb-3">
                            <label for="image" class="form-label">Bild:</label>
                            <input type="file" id="image" wire:model="image" class="form-control">
                            @if($existingImage)
                                <img src="{{ asset('storage/' . $existingImage) }}" class="img-thumbnail mt-2" width="100" alt="Image Preview">
                            @endif
                            @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Filter Months (Multiple) -->
                    <div class="mb-3">
                        <label for="filter_months" class="form-label">Filter Monate (mehrfach auswählbar):</label>
                        <select id="filter_months" wire:model="filter_months" class="form-select" multiple>
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}">Monat {{ $month }}</option>
                            @endforeach
                        </select>
                        @error('filter_months') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Kategorie (Slug):</label>
                        <select id="slug" wire:model="slug" class="form-select" {{ empty($slugOptions) ? 'disabled' : '' }}>
                            <option value="">Bitte wählen...</option>

                            @if($editId && !isset($slugOptions[$slug]))
                                {{-- Falls im Bearbeitungsmodus, füge den aktuellen Slug wieder hinzu --}}
                                <option value="{{ $slug }}" selected>{{ $slugOptions[$slug] ?? ucfirst(str_replace('-', ' ', $slug)) }}</option>
                            @endif

                            @foreach($slugOptions as $slugValue => $slugLabel)
                                <option value="{{ $slugValue }}">{{ $slugLabel }}</option>
                            @endforeach
                        </select>
                        @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    @if(empty($slugOptions) && !$editId)
                        <div class="alert alert-warning">
                            Alle Kategorien wurden bereits verwendet. Es können keine neuen Einträge mehr erstellt werden.
                        </div>
                    @endif



                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select id="status" wire:model="status" class="form-select">
                            <option value="1">Aktiv</option>
                            <option value="0">Inaktiv</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">@if($editId) Aktualisieren @else Speichern @endif</button>
                    <button type="button" wire:click="resetFields" class="btn btn-secondary">Abbrechen</button>
                </form>
            @else
                <button wire:click="$set('showForm', true)" class="btn btn-primary">Neues Item hinzufügen</button>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Gespeicherte Einträge</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 30px;">☰</th> <!-- Sortiergriff -->
                        <th>Bild</th>
                        <th>Titel</th>
                        <th>Titel-Text</th>
                        <th>Normaler Text</th>
                        <th>Monate</th>
                        <th>Status</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody class="sortable-table" x-data x-init="initSortable()">
                    @foreach($galleryItems as $item)
                        <tr data-id="{{ $item->id }}" class="sortable-item">
                            <td class="handle text-center" style="cursor: grab;">☰</td>
                            <td>
                                @if($item->thumbnail)
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" class="img-thumbnail" width="50" alt="Thumbnail">
                                @elseif($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" class="img-thumbnail" width="50" alt="Image">
                                @endif
                            </td>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->title_text }}</td>
                            <td>{!! $item->content !!}</td>
                            <td>
                                @if($item->filter_months)
                                    @foreach($item->filter_months as $m)
                                        <span class="badge bg-info text-dark me-1">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <button wire:click="toggleStatus({{ $item->id }})"
                                    class="btn btn-sm {{ $item->status ? 'btn-success' : 'btn-danger' }}">
                                    {{ $item->status ? 'Aktiv' : 'Inaktiv' }}
                                </button>
                            </td>
                            <td>
                                <button wire:click="edit({{ $item->id }})" class="btn btn-info btn-sm">Bearbeiten</button>
                                <button wire:click="delete({{ $item->id }})" class="btn btn-danger btn-sm">Löschen</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $galleryItems->links('vendor.livewire.custom-pagination') }}


    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

    <script>
        function initSortable() {
            console.log("initSortable wurde aufgerufen!");

            let sortableList = document.querySelector(".sortable-table");
            if (!sortableList) {
                console.warn("Kein .sortable-table gefunden!");
                return;
            }

            Sortable.create(sortableList, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                handle: '.handle', // Nur über den Griff verschiebbar
                onEnd: function(evt) {
                    let orderedIds = Array.from(evt.target.children).map(row => row.dataset.id);
                    console.log("Neue Reihenfolge:", orderedIds);
                    Livewire.dispatch('updateOrder', { items: orderedIds });
                }
            });
        }
    </script>

    <style>
        .sortable-ghost {
            opacity: 0.5;
            background: #c8ebfb;
        }
        .handle {
            font-size: 18px;
            cursor: grab;
        }
    </style>
</div>
@assets
<!-- Include Jodit CSS Styling -->
<link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">

<!-- Include the Jodit JS Library -->
<script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
@endassets
