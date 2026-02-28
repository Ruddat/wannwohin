<div class="container py-4">
    <div class="d-flex flex-wrap gap-2 align-items-end mb-3">
        <div>
            <label class="form-label mb-1">Suche</label>
            <input type="text" class="form-control" wire:model.live.debounce.300ms="q" placeholder="z.B. golf, park, kultur">
        </div>

        <div>
            <label class="form-label mb-1">Gruppe</label>
            <select class="form-select" wire:model.live="group">
                <option value="">Alle</option>
                @foreach($groups as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label mb-1">Sortierung</label>
            <select class="form-select" wire:model.live="sort">
                <option value="usage">Nutzung</option>
                <option value="title">Titel</option>
                <option value="group">Gruppe</option>
            </select>
        </div>

        <div>
            <label class="form-label mb-1">Richtung</label>
            <select class="form-select" wire:model.live="dir">
                <option value="desc">DESC</option>
                <option value="asc">ASC</option>
            </select>
        </div>

        <div>
            <label class="form-label mb-1">Pro Seite</label>
            <select class="form-select" wire:model.live="perPage">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

        <div class="form-check ms-auto">
            <input class="form-check-input" type="checkbox" wire:model.live="showSuggestions" id="showSug">
            <label class="form-check-label" for="showSug">Vorschläge anzeigen</label>
        </div>
    </div>

    @if($showSuggestions && !empty($this->suggestions))
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Merge-Vorschläge (Top 30)</strong>
                <small class="text-muted">Ähnlichkeit/Normalisierung</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Gruppe</th>
                                <th>Quelle</th>
                                <th>Ziel</th>
                                <th>Grund</th>
                                <th class="text-end">Aktion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->suggestions as $s)
                                <tr>
                                    <td>{{ $s['group'] }}</td>
                                    <td><span class="badge bg-warning text-dark">#{{ $s['source_id'] }}</span> {{ $s['source_title'] }}</td>
                                    <td><span class="badge bg-success">#{{ $s['target_id'] }}</span> {{ $s['target_title'] }}</td>
                                    <td>{{ $s['reason'] }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-danger"
                                                wire:click="$set('mergeTarget.{{ $s['source_id'] }}', {{ $s['target_id'] }}); $dispatch('do-merge-{{ $s['source_id'] }}')">
                                            Ziel setzen
                                        </button>
                                        <button class="btn btn-sm btn-primary"
                                                wire:click="merge({{ $s['source_id'] }})"
                                                onclick="return confirm('Merge wirklich durchführen? Quelle wird gelöscht.');">
                                            Merge
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-muted small">
                    Tipp: „Ziel setzen“ füllt nur das Dropdown; „Merge“ führt aus.
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong>Tags</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gruppe</th>
                        <th>Titel</th>
                        <th>Slug</th>
                        <th class="text-end">Nutzung</th>
                        <th style="width: 320px;">Merge → Ziel</th>
                        <th style="width: 220px;">Rename</th>
                        <th class="text-end">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tags as $tag)
                        <tr>
                            <td>#{{ $tag->id }}</td>
                            <td>{{ $tag->group }}</td>
                            <td>{{ $tag->title }}</td>
                            <td class="text-muted">{{ $tag->slug }}</td>
                            <td class="text-end">{{ number_format($tag->usage_count, 0, ',', '.') }}</td>

                            <td>
                                <select class="form-select form-select-sm" wire:model="mergeTarget.{{ $tag->id }}">
                                    <option value="">— Ziel wählen —</option>
                                    @foreach($targetOptions as $opt)
                                        @if($opt->id !== $tag->id)
                                            <option value="{{ $opt->id }}">{{ $opt->group }} → {{ $opt->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input class="form-control form-control-sm mb-1"
                                       placeholder="Neuer Titel"
                                       wire:model.defer="renameTitle.{{ $tag->id }}">
                                <input class="form-control form-control-sm"
                                       placeholder="Neue Gruppe"
                                       wire:model.defer="renameGroup.{{ $tag->id }}">
                            </td>

                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                        wire:click="rename({{ $tag->id }})">
                                    Speichern
                                </button>

                                <button class="btn btn-sm btn-danger"
                                        wire:click="merge({{ $tag->id }})"
                                        onclick="return confirm('Merge wirklich durchführen? Quelle wird gelöscht.');">
                                    Merge
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-body">
            {{ $tags->links() }}
        </div>
    </div>
</div>
