
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
                Location Manager
                <span class="text-muted">/ Standort bearbeiten: {{ $location && $location->title ? $location->title : 'Unbekannter Standort' }}</span>
            </h2>
          </div>
        </div>
      </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
          <div class="row row-cards">
            <div class="col-md-12">

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#tab-info" class="nav-link {{ $activeTab === 'info' ? 'active' : '' }}" data-bs-toggle="tab" wire:click.prevent="setActiveTab('info')" role="tab">
                    <!-- Icon für Standortinformationen -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    Standortinfo
                </a>
            </li>

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


            <div class="tab-pane {{ $activeTab === 'texts' ? 'active show' : '' }}" id="tab-texts" role="tabpanel">
                <livewire:backend.location-manager.partials.location-edit-texts :locationId="$locationId" />
            </div>

            <div class="tab-pane {{ $activeTab === 'info' ? 'active show' : '' }}" id="tab-info" role="tabpanel">
                <livewire:backend.location-manager.partials.location-edit-info :locationId="$locationId" />
            </div>

            <div class="tab-pane {{ $activeTab === 'images' ? 'active show' : '' }}" id="tab-images" role="tabpanel">
                <!-- Bilder-Komponente -->
                <livewire:backend.location-manager.partials.location-edit-images-component :locationId="$locationId" />
            </div>
            <div class="tab-pane {{ $activeTab === 'data' ? 'active show' : '' }}" id="tab-data" role="tabpanel">
                <livewire:backend.location-manager.partials.location-edit-data-component :locationId="$locationId" />
            </div>
            <div class="tab-pane {{ $activeTab === 'gallery' ? 'active show' : '' }}" id="tab-gallery" role="tabpanel">
                <livewire:backend.location-manager.partials.location-edit-gallery-component :locationId="$locationId" />
            </div>
            <div class="tab-pane {{ $activeTab === 'tags' ? 'active show' : '' }}" id="tab-tags" role="tabpanel">
                <livewire:backend.location-manager.partials.location-edit-tags-component :locationId="$locationId" />
            </div>

            <div class="tab-pane {{ $activeTab === 'filters' ? 'active show' : '' }}" id="tab-filters" role="tabpanel">
                <livewire:backend.location-manager.partials.location-filter-manager-component :locationId="$locationId" />
            </div>


        </div>
    </div>
</div>
</div>
          </div>
        </div>
    </div>
</div>


