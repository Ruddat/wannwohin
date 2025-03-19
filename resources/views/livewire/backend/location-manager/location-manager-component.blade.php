<div>
    @if($isModalOpen)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1" role="dialog" wire:ignore.self>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Standort bearbeiten: {{ $location && $location->title ? $location->title : 'Unbekannter Standort' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                                    <li class="nav-item">
                                        <a href="#tab-info" class="nav-link {{ $activeTab === 'info' ? 'active' : '' }}" wire:click.prevent="setActiveTab('info')">
                                            Standortinfo
                                        </a>
                                    </li>
                                    <!-- Weitere Tabs hier einfügen -->

                                    <li class="nav-item" role="presentation">
                                        <a href="#tab-texts" class="nav-link {{ $activeTab === 'texts' ? 'active' : '' }}" data-bs-toggle="tab" wire:click.prevent="setActiveTab('texts')" role="tab">
                                            <!-- Icon für Texte -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 7h16M4 12h16M4 17h16"></path>
                                            </svg>
                                            Texte
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="#tab-images" class="nav-link {{ $activeTab === 'images' ? 'active' : '' }}" data-bs-toggle="tab" wire:click.prevent="setActiveTab('images')" role="tab">
                                            <!-- Icon für Bilder -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15l-5-5L5 21V15H3v6h6v-2H7z"></path>
                                            </svg>
                                            Bilder
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="#tab-data" class="nav-link {{ $activeTab === 'data' ? 'active' : '' }}" data-bs-toggle="tab" wire:click.prevent="setActiveTab('data')" role="tab">
                                            <!-- Icon für Daten -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="7" height="7"></rect>
                                                <rect x="14" y="3" width="7" height="7"></rect>
                                                <rect x="3" y="14" width="7" height="7"></rect>
                                                <rect x="14" y="14" width="7" height="7"></rect>
                                            </svg>
                                            Daten
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="#tab-gallery" class="nav-link {{ $activeTab === 'gallery' ? 'active' : '' }}" data-bs-toggle="tab" wire:click.prevent="setActiveTab('gallery')" role="tab">
                                            <!-- Icon für Galerie -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <path d="M14.31 8L20 8M4 8h7.69M5.41 19h13.18M12 5V3M12 21v-2"></path>
                                            </svg>
                                            Galerie
                                        </a>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <a href="#tab-tags" class="nav-link {{ $activeTab === 'tags' ? 'active' : '' }}" data-bs-toggle="tab" wire:click.prevent="setActiveTab('tags')" role="tab">
                                            <!-- Icon für Tags -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 12l18-9-9 18-3-6-6-3z"></path>
                                            </svg>
                                            Tags & Reisezeiten
                                        </a>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <a href="#tab-filters" class="nav-link {{ $activeTab === 'filters' ? 'active' : '' }}" data-bs-toggle="tab" wire:click.prevent="setActiveTab('filters')" role="tab">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 7h16M4 12h16M4 17h16"></path>
                                            </svg>
                                            Filter
                                        </a>
                                    </li>










                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane {{ $activeTab === 'info' ? 'active show' : '' }}" id="tab-info">
                                        <livewire:backend.location-manager.partials.location-edit-info :locationId="$locationId" />
                                    </div>
                                    <!-- Weitere Tab-Panes hier einfügen -->

                                    <div class="tab-pane {{ $activeTab === 'texts' ? 'active show' : '' }}" id="tab-texts" role="tabpanel">
                                        <livewire:backend.location-manager.partials.location-edit-texts :locationId="$locationId" wire:lazy />
                                    </div>

                                    <div class="tab-pane {{ $activeTab === 'images' ? 'active show' : '' }}" id="tab-images" role="tabpanel">
                                        <!-- Bilder-Komponente -->
                                        <livewire:backend.location-manager.partials.location-edit-images-component :locationId="$locationId" wire:lazy />
                                    </div>
                                    <div class="tab-pane {{ $activeTab === 'data' ? 'active show' : '' }}" id="tab-data" role="tabpanel">
                                        <livewire:backend.location-manager.partials.location-edit-data-component :locationId="$locationId" wire:lazy />
                                    </div>
                                    <div class="tab-pane {{ $activeTab === 'gallery' ? 'active show' : '' }}" id="tab-gallery" role="tabpanel">
                                        <livewire:backend.location-manager.partials.location-edit-gallery-component :locationId="$locationId" wire:lazy />
                                    </div>
                                    <div class="tab-pane {{ $activeTab === 'tags' ? 'active show' : '' }}" id="tab-tags" role="tabpanel">
                                        <livewire:backend.location-manager.partials.location-edit-tags-component :locationId="$locationId" wire:lazy />
                                    </div>

                                    <div class="tab-pane {{ $activeTab === 'filters' ? 'active show' : '' }}" id="tab-filters" role="tabpanel">
                                        <livewire:backend.location-manager.partials.location-filter-manager-component :locationId="$locationId" wire:lazy />
                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeModal">Schließen</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@assets
<!-- Include Jodit CSS Styling -->
<link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">

<!-- Include the Jodit JS Library -->
<script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
@endassets
