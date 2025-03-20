<div>

<!-- Ladeanzeige -->
@if($isLoading)
<div class="modal modal-blur fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Lade Standortdaten...</p>
            </div>
        </div>
    </div>
</div>
@endif



    @if($isModalOpen && $locationId)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1" role="dialog" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom-0 bg-light">
                        <h5 class="modal-title fw-bold">
                            Standort bearbeiten: {{ $location && $location->title ? $location->title : 'Unbekannter Standort' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="card border-0 rounded-0">
<!-- Tabs mit ra-admin Styling und Font Awesome-Icons -->
<div class="card-header p-0 bg-transparent border-bottom">
    <ul class="nav nav-tabs tab-outline-primary" id="locationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'info' ? 'active' : '' }}"
                    id="info-tab" wire:click.prevent="setActiveTab('info')"
                    data-bs-toggle="tab" data-bs-target="#tab-info"
                    type="button" role="tab" aria-controls="tab-info"
                    aria-selected="{{ $activeTab === 'info' ? 'true' : 'false' }}">
                <i class="fas fa-info-circle me-2"></i> Standortinfo
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'texts' ? 'active' : '' }}"
                    id="texts-tab" wire:click.prevent="setActiveTab('texts')"
                    data-bs-toggle="tab" data-bs-target="#tab-texts"
                    type="button" role="tab" aria-controls="tab-texts"
                    aria-selected="{{ $activeTab === 'texts' ? 'true' : 'false' }}">
                <i class="fas fa-file-alt me-2"></i> Texte
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'images' ? 'active' : '' }}"
                    id="images-tab" wire:click.prevent="setActiveTab('images')"
                    data-bs-toggle="tab" data-bs-target="#tab-images"
                    type="button" role="tab" aria-controls="tab-images"
                    aria-selected="{{ $activeTab === 'images' ? 'true' : 'false' }}">
                <i class="fas fa-image me-2"></i> Bilder
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'data' ? 'active' : '' }}"
                    id="data-tab" wire:click.prevent="setActiveTab('data')"
                    data-bs-toggle="tab" data-bs-target="#tab-data"
                    type="button" role="tab" aria-controls="tab-data"
                    aria-selected="{{ $activeTab === 'data' ? 'true' : 'false' }}">
                <i class="fas fa-database me-2"></i> Daten
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'gallery' ? 'active' : '' }}"
                    id="gallery-tab" wire:click.prevent="setActiveTab('gallery')"
                    data-bs-toggle="tab" data-bs-target="#tab-gallery"
                    type="button" role="tab" aria-controls="tab-gallery"
                    aria-selected="{{ $activeTab === 'gallery' ? 'true' : 'false' }}">
                <i class="fas fa-images me-2"></i> Galerie
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'tags' ? 'active' : '' }}"
                    id="tags-tab" wire:click.prevent="setActiveTab('tags')"
                    data-bs-toggle="tab" data-bs-target="#tab-tags"
                    type="button" role="tab" aria-controls="tab-tags"
                    aria-selected="{{ $activeTab === 'tags' ? 'true' : 'false' }}">
                <i class="fas fa-tags me-2"></i> Tags & Reisezeiten
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'filters' ? 'active' : '' }}"
                    id="filters-tab" wire:click.prevent="setActiveTab('filters')"
                    data-bs-toggle="tab" data-bs-target="#tab-filters"
                    type="button" role="tab" aria-controls="tab-filters"
                    aria-selected="{{ $activeTab === 'filters' ? 'true' : 'false' }}">
                <i class="fas fa-filter me-2"></i> Filter
            </button>
        </li>
    </ul>
</div>
                            <!-- Tab-Inhalte -->
                            <div class="card-body p-4">
                                <div class="tab-content" wire:ignore>
                                    <div class="tab-pane fade {{ $activeTab === 'info' ? 'show active' : '' }}"
                                         id="tab-info" role="tabpanel" aria-labelledby="info-tab"
                                         wire:key="tab-info-{{ $locationId }}">
                                        @livewire('backend.location-manager.partials.location-edit-info', ['locationId' => $locationId], key('info-' . $locationId))
                                    </div>
                                    <div class="tab-pane fade {{ $activeTab === 'texts' ? 'show active' : '' }}"
                                         id="tab-texts" role="tabpanel" aria-labelledby="texts-tab"
                                         wire:key="tab-texts-{{ $locationId }}">
                                        @livewire('backend.location-manager.partials.location-edit-texts', ['locationId' => $locationId], key('texts-' . $locationId))
                                    </div>
                                    <div class="tab-pane fade {{ $activeTab === 'images' ? 'show active' : '' }}"
                                         id="tab-images" role="tabpanel" aria-labelledby="images-tab"
                                         wire:key="tab-images-{{ $locationId }}">
                                        @livewire('backend.location-manager.partials.location-edit-images-component', ['locationId' => $locationId], key('images-' . $locationId))
                                    </div>
                                    <div class="tab-pane fade {{ $activeTab === 'data' ? 'show active' : '' }}"
                                         id="tab-data" role="tabpanel" aria-labelledby="data-tab"
                                         wire:key="tab-data-{{ $locationId }}">
                                        @livewire('backend.location-manager.partials.location-edit-data-component', ['locationId' => $locationId], key('data-' . $locationId))
                                    </div>
                                    <div class="tab-pane fade {{ $activeTab === 'gallery' ? 'show active' : '' }}"
                                         id="tab-gallery" role="tabpanel" aria-labelledby="gallery-tab"
                                         wire:key="tab-gallery-{{ $locationId }}">
                                        @livewire('backend.location-manager.partials.location-edit-gallery-component', ['locationId' => $locationId], key('gallery-' . $locationId))
                                    </div>
                                    <div class="tab-pane fade {{ $activeTab === 'tags' ? 'show active' : '' }}"
                                         id="tab-tags" role="tabpanel" aria-labelledby="tags-tab"
                                         wire:key="tab-tags-{{ $locationId }}">
                                        @livewire('backend.location-manager.partials.location-edit-tags-component', ['locationId' => $locationId], key('tags-' . $locationId))
                                    </div>
                                    <div class="tab-pane fade {{ $activeTab === 'filters' ? 'show active' : '' }}"
                                         id="tab-filters" role="tabpanel" aria-labelledby="filters-tab"
                                         wire:key="tab-filters-{{ $locationId }}">
                                        @livewire('backend.location-manager.partials.location-filter-manager-component', ['locationId' => $locationId], key('filters-' . $locationId))
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light">
                        <button class="btn btn-link link-secondary" wire:click="closeModal">Schließen</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@assets
    <link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">
    <script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
@endassets

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const triggerTabList = document.querySelectorAll('.nav-tabs .nav-link');
            triggerTabList.forEach(triggerEl => {
                triggerEl.addEventListener('click', event => {
                    event.preventDefault();
                    const tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                });
            });
        });
    </script>
@endpush
