<div class="container-xl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">SEO-Einträge</h3>
                    <div class="card-actions">
                        <input type="text" wire:model.debounce.300ms="search" class="form-control w-auto" placeholder="Suche..." style="max-width: 300px;">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped">
                        <thead>
                            <tr>
                                <th class="w-1 cursor-pointer" wire:click="sortBy('id')">
                                    ID
                                    @if($sortField === 'id')
                                        <span class="text-muted">
                                            {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                                        </span>
                                    @endif
                                </th>
                                <th class="w-1 cursor-pointer" wire:click="sortBy('model_type')">
                                    Model-Typ
                                    @if($sortField === 'model_type')
                                        <span class="text-muted">
                                            {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                                        </span>
                                    @endif
                                </th>
                                <th class="w-1 cursor-pointer" wire:click="sortBy('model_id')">
                                    Model-ID
                                    @if($sortField === 'model_id')
                                        <span class="text-muted">
                                            {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                                        </span>
                                    @endif
                                </th>
                                <th class="w-1 cursor-pointer" wire:click="sortBy('title')">
                                    Titel
                                    @if($sortField === 'title')
                                        <span class="text-muted">
                                            {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                                        </span>
                                    @endif
                                </th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($seoMetas as $seoMeta)
                                <tr>
                                    <td>{{ $seoMeta->id }}</td>
                                    <td>{{ $seoMeta->model_type }}</td>
                                    <td>{{ $seoMeta->model_id }}</td>
                                    <td>{{ $seoMeta->title }}</td>
                                    <td>
                                        <button wire:click="edit({{ $seoMeta->id }})" class="btn btn-primary btn-sm">Bearbeiten</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $seoMetas->links() }}

                    <div class="ms-auto">
                        <select wire:model="perPage" class="form-select w-auto">
                            <option value="10">10 pro Seite</option>
                            <option value="25">25 pro Seite</option>
                            <option value="50">50 pro Seite</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
