



<div class="page-body">
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="container-fluid">
            <!-- Breadcrumb start -->
            <div class="row m-1">
                <div class="col-12 ">
                </div>
              </div>
              <!-- Breadcrumb end -->

             <div class="row row-cards">
            <!-- Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Countries</h3>
                        <!-- Optional: Hier könnte ein zusätzlicher Button hinzugefügt werden -->
                    </div>

                    <div class="card-body border-bottom py-3">
                        <div class="row align-items-end">
                            <!-- Filter für Kontinent -->
                            <div class="col-md-2 col-sm-6 mb-3">
                                <label for="filterContinent" class="form-label">Kontinent</label>
                                <select wire:model.live="filterContinent" id="filterContinent" class="form-select">
                                    <option value="">Alle Kontinente</option>
                                    @foreach ($continents as $continent)
                                        <option value="{{ $continent->id }}">{{ $continent->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter für Status -->
                            <div class="col-md-2 col-sm-6 mb-3">
                                <label for="filterStatus" class="form-label">Status</label>
                                <select wire:model.live="filterStatus" id="filterStatus" class="form-select">
                                    <option value="">Alle</option>
                                    <option value="active">Aktiv</option>
                                    <option value="pending">Ausstehend</option>
                                    <option value="inactive">Inaktiv</option>
                                </select>
                            </div>

                            <!-- Filter für Bevölkerung -->
                            <div class="col-md-2 col-sm-6 mb-3">
                                <label for="filterPopulation" class="form-label">Bevölkerung</label>
                                <select wire:model.live="filterPopulation" id="filterPopulation" class="form-select">
                                    <option value="">Alle</option>
                                    <option value="low">Weniger als 1M</option>
                                    <option value="medium">1M - 10M</option>
                                    <option value="high">Mehr als 10M</option>
                                </select>
                            </div>

                            <!-- Suchfeld -->
                            <div class="col-md-4 col-sm-6 mb-3">
                                <label for="search" class="form-label">Suche</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input wire:model.live="search" type="text" id="search" class="form-control" placeholder="Name oder Code...">
                                </div>
                            </div>

                            <!-- Export-Button -->
                            <div class="col-md-2 col-sm-6 mb-3 d-flex justify-content-end">
                                <div>
                                    <label class="form-label invisible d-block">Export</label> <!-- Invisible Label für Ausrichtung -->
                                    <button wire:click="exportToExcel" class="btn btn-success w-100">
                                        <i class="ti ti-download me-1"></i> Excel herunterladen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Continent</th>
                                    <th>Code</th>
                                    <th>Population</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($countries as $country)
                                    <tr>
                                        <td>{{ $country->id }}</td>
                                        <td>
                                            <img src="{{ $country->thumbnail }}" alt="{{ $country->title }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>{{ $country->title }}</td>
                                        <td>{{ $country->continent->title ?? 'N/A' }}</td>
                                        <td>{{ $country->country_code }}</td>
                                        <td>{{ $country->population ?? 'N/A' }}</td>
                                        <td>
                                            <button wire:click="toggleStatus({{ $country->id }})" class="btn btn-sm @if ($country->status === 'active') btn-success @elseif ($country->status === 'pending') btn-warning @else btn-secondary @endif">
                                                {{ ucfirst($country->status) }}
                                            </button>
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <button wire:click="edit({{ $country->id }})" class="btn btn-icon btn-primary btn-sm" title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button wire:click="delete({{ $country->id }})" class="btn btn-icon btn-danger btn-sm" title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No countries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <select wire:model.change="perPage" class="form-select form-select-sm">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="app-pagination-link">
                                    <ul class="pagination app-pagination justify-content-center mb-0">
                                        <li class="page-item {{ $countries->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link b-r-left" wire:click="previousPage" href="#" aria-label="Previous">
                                                Previous
                                            </a>
                                        </li>
                                        @php
                                            $currentPage = $countries->currentPage();
                                            $lastPage = $countries->lastPage();
                                            $range = 2;
                                            $start = max(1, $currentPage - $range);
                                            $end = min($lastPage, $currentPage + $range);
                                        @endphp
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}" aria-current="{{ $currentPage == $i ? 'page' : '' }}">
                                                <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                                            </li>
                                        @endfor
                                        <li class="page-item {{ $countries->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link b-r-right" wire:click="nextPage" href="#" aria-label="Next">
                                                Next
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <span class="text-muted">
                                    Zeigt {{ $countries->firstItem() }} bis {{ $countries->lastItem() }} von {{ $countries->total() }} Einträgen
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Neues Formular zum Importieren von Ländern -->
        <div class="row row-cards mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Import Countries</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('countries.import') }}" method="POST" enctype="multipart/form-data" class="p-4">
                            @csrf
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Upload Excel File</label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control" required>
                                @error('excel_file')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <livewire:backend.stuff-updater.country-image-updater />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit/Create Modal -->
    @if ($editMode)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $countryId ? 'Edit Country' : 'Create Country' }}</h5>
                        <button type="button" class="btn-close" wire:click="resetInputFields" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input wire:model="title" type="text" id="title" class="form-control" placeholder="Country Title">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="continent_id" class="form-label">Continent</label>
                                <select wire:model="continent_id" id="continent_id" class="form-select">
                                    <option value="">Select Continent</option>
                                    @foreach ($continents as $continent)
                                        <option value="{{ $continent->id }}">{{ $continent->title }}</option>
                                    @endforeach
                                </select>
                                @error('continent_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="country_code" class="form-label">Country Code (ISO2)</label>
                                <input wire:model="country_code" type="text" id="country_code" class="form-control" placeholder="Country Code (e.g., US)">
                                @error('country_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country_iso_3" class="form-label">Country ISO3 Code</label>
                                <input wire:model="country_iso_3" type="text" id="country_iso_3" class="form-control" placeholder="Country ISO3 Code (e.g., USA)">
                                @error('country_iso_3')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Currency Code -->
                        <div class="mb-3">
                            <label for="currency_code" class="form-label">Currency Code</label>
                            <input wire:model="currency_code" type="text" id="currency_code" class="form-control" placeholder="Currency Code (e.g., USD)">
                            @error('currency_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Currency Name -->
                        <div class="mb-3">
                            <label for="currency_name" class="form-label">Currency Name</label>
                            <input wire:model="currency_name" type="text" id="currency_name" class="form-control" placeholder="Currency Name (e.g., US Dollar)">
                            @error('currency_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Currency Conversion -->
                        <div class="mb-3">
                            <label for="currency_conversion" class="form-label">Currency Conversion</label>
                            <input wire:model="currency_conversion" type="text" id="currency_conversion" class="form-control" placeholder="Currency Conversion (e.g., 1 USD = 0.85 EUR)">
                            @error('currency_conversion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Population -->
                        <div class="mb-3">
                            <label for="population" class="form-label">Population</label>
                            <input wire:model="population" type="number" id="population" class="form-control" placeholder="Population">
                            @error('population')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Capital -->
                        <div class="mb-3">
                            <label for="capital" class="form-label">Capital</label>
                            <input wire:model="capital" type="text" id="capital" class="form-control" placeholder="Capital">
                            @error('capital')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Population Capital -->
                        <div class="mb-3">
                            <label for="population_capital" class="form-label">Population Capital</label>
                            <input wire:model="population_capital" type="number" id="population_capital" class="form-control" placeholder="Population Capital">
                            @error('population_capital')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Area -->
                        <div class="mb-3">
                            <label for="area" class="form-label">Area (in km²)</label>
                            <input wire:model="area" type="number" id="area" class="form-control" placeholder="Area">
                            @error('area')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Official Language -->
                        <div class="mb-3">
                            <label for="official_language" class="form-label">Official Language</label>
                            <input wire:model="official_language" type="text" id="official_language" class="form-control" placeholder="Official Language">
                            @error('official_language')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Language EZMZ -->
                        <div class="mb-3">
                            <label for="language_ezmz" class="form-label">Language EZMZ</label>
                            <input wire:model="language_ezmz" type="text" id="language_ezmz" class="form-control" placeholder="Language EZMZ">
                            @error('language_ezmz')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- BSP in USD -->
                        <div class="mb-3">
                            <label for="bsp_in_USD" class="form-label">BSP in USD</label>
                            <input wire:model="bsp_in_USD" type="number" id="bsp_in_USD" class="form-control" placeholder="BSP in USD">
                            @error('bsp_in_USD')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Life Expectancy (Men) -->
                        <div class="mb-3">
                            <label for="life_expectancy_m" class="form-label">Life Expectancy (Men)</label>
                            <input wire:model="life_expectancy_m" type="number" step="0.1" id="life_expectancy_m" class="form-control" placeholder="Life Expectancy (Men)">
                            @error('life_expectancy_m')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Life Expectancy (Women) -->
                        <div class="mb-3">
                            <label for="life_expectancy_w" class="form-label">Life Expectancy (Women)</label>
                            <input wire:model="life_expectancy_w" type="number" step="0.1" id="life_expectancy_w" class="form-control" placeholder="Life Expectancy (Women)">
                            @error('life_expectancy_w')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Population Density -->
                        <div class="mb-3">
                            <label for="population_density" class="form-label">Population Density</label>
                            <input wire:model="population_density" type="number" step="0.1" id="population_density" class="form-control" placeholder="Population Density">
                            @error('population_density')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Visum benötigt -->
                        <div class="mb-3">
                            <label for="country_visum_needed" class="form-label">Visum benötigt</label>
                            <select wire:model="country_visum_needed" id="country_visum_needed" class="form-select">
                                <option value="0">Nein</option>
                                <option value="1">Ja</option>
                            </select>
                            @error('country_visum_needed')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Maximale Visumdauer -->
                        <div class="mb-3">
                            <label for="country_visum_max_time" class="form-label">Maximale Visumdauer</label>
                            <input wire:model="country_visum_max_time" type="text" id="country_visum_max_time" class="form-control" placeholder="Maximale Visumdauer">
                            @error('country_visum_max_time')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Preis-Tendenz -->
                        <div class="mb-3">
                            <label for="price_tendency" class="form-label">Preis-Tendenz</label>
                            <input wire:model="price_tendency" type="text" id="price_tendency" class="form-control" placeholder="Preis-Tendenz">
                            @error('price_tendency')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="country_code" class="form-label">Country Code (ISO2)</label>
                            <div class="input-group">
                                <input wire:model="country_code" type="text" id="country_code" class="form-control" placeholder="Country Code (z.B. US)">
                                <button wire:click="fetchCountryData" class="btn btn-primary">
                                    <i class="ti ti-download"></i> Automatisch füllen
                                </button>
                            </div>
                            @error('country_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Country Header Einstellungen -->
                        <div class="mb-4 p-3 border rounded bg-light">
                            <h5 class="mb-3">Country Header Einstellungen</h5>
                            <p class="text-muted">
                                Der <strong>Header Titel</strong> ersetzt den Standardnamen des Landes. <br>
                                Der <strong>Header Text</strong> ersetzt die Standardbeschreibung im Panorama unter dem Titel.
                            </p>

                            <!-- Country Header Titel -->
                            <div class="mb-3">
                                <label for="country_headert_titel" class="form-label">Header Titel</label>
                                <input wire:model="country_headert_titel" type="text" id="country_headert_titel" class="form-control" placeholder="Neuer Header Titel">
                                @error('country_headert_titel') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Country Header Text mit Jodit -->
                            <div class="mb-3">
                                <label for="country_header_text" class="form-label">Header Beschreibung</label>
                                <livewire:jodit-text-editor wire:model.live="country_header_text" :buttons="[
                                    'bold', 'italic', 'underline', '|',
                                    'font', 'fontsize', '|',
                                    'paragraph', '|',
                                    'left', 'center', 'right', 'justify', '|',
                                    'ul', 'ol', '|',
                                    'link', '|',
                                    'undo', 'redo', 'eraser'
                                ]" />
                                @error('country_header_text') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Bereich für Panorama Bild -->
                            <div class="mb-4">
                                <h5>Panorama Bild</h5>
                                @if ($custom_images)
                                    <label for="newPanoramaImage" class="form-label">Panorama Bild hochladen</label>
                                    <input type="file" wire:model="newPanoramaImage" id="newPanoramaImage" class="form-control">
                                    @error('newPanoramaImage') <span class="text-danger">{{ $message }}</span> @enderror

                                    @if ($newPanoramaImage)
                                        <div class="mt-3 text-center">
                                            <img src="{{ $newPanoramaImage->temporaryUrl() }}" alt="Panorama Vorschau" class="img-thumbnail" style="max-width: 150px;">
                                        </div>
                                    @elseif ($panorama_image_path)
                                        <div class="mt-3 text-center">
                                            <img src="{{ asset('storage/' . $panorama_image_path) }}" alt="Panorama Bild" class="img-thumbnail" style="max-width: 150px;">
                                            <button wire:click="deletePanoramaImage" class="btn btn-sm btn-danger mt-2">
                                                <i class="ti ti-trash"></i> Löschen
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    <label for="panoramaSearchKeyword" class="form-label">Panorama Bild über Pixabay suchen</label>
                                    <div class="input-group">
                                        <input wire:model="panoramaSearchKeyword" type="text" id="panoramaSearchKeyword" class="form-control" placeholder="Suchbegriff für Panorama Bild">
                                        <button wire:click="fetchPanoramaImages" class="btn btn-outline-secondary">Suchen</button>
                                    </div>
                                    @if ($panoramaPixabayImages)
                                        <div class="row mt-3">
                                            @foreach ($panoramaPixabayImages as $index => $image)
                                                <div class="col-md-3 text-center">
                                                    <img src="{{ $image['previewURL'] }}" alt="Panorama Image {{ $index }}" class="img-thumbnail" style="max-width: 100px;">
                                                    <button wire:click="selectPanoramaImage({{ $index }})" class="btn btn-sm btn-primary mt-2">Auswählen</button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if ($panorama_image_path)
                                        <div class="mt-3 text-center">
                                            <img src="{{ asset('storage/' . $panorama_image_path) }}" alt="Ausgewähltes Panorama Bild" class="img-thumbnail" style="max-width: 150px;">
                                            <button wire:click="deletePanoramaImage" class="btn btn-sm btn-danger mt-2">
                                                <i class="ti ti-trash"></i> Löschen
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Bereich für Header Bild -->
                            <div class="mb-4">
                                <h5>Header Bild</h5>
                                @if ($custom_images)
                                    <label for="newHeaderImage" class="form-label">Header Bild hochladen</label>
                                    <input type="file" wire:model="newHeaderImage" id="newHeaderImage" class="form-control">
                                    @error('newHeaderImage') <span class="text-danger">{{ $message }}</span> @enderror

                                    @if ($newHeaderImage)
                                        <div class="mt-3 text-center">
                                            <img src="{{ $newHeaderImage->temporaryUrl() }}" alt="Header Vorschau" class="img-thumbnail" style="max-width: 150px;">
                                        </div>
                                    @elseif ($header_image_path)
                                        <div class="mt-3 text-center">
                                            <img src="{{ asset('storage/' . $header_image_path) }}" alt="Header Bild" class="img-thumbnail" style="max-width: 150px;">
                                            <button wire:click="deleteHeaderImage" class="btn btn-sm btn-danger mt-2">
                                                <i class="ti ti-trash"></i> Löschen
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    <label for="headerSearchKeyword" class="form-label">Header Bild über Pixabay suchen</label>
                                    <div class="input-group">
                                        <input wire:model="headerSearchKeyword" type="text" id="headerSearchKeyword" class="form-control" placeholder="Suchbegriff für Header Bild">
                                        <button wire:click="fetchHeaderImages" class="btn btn-outline-secondary">Suchen</button>
                                    </div>
                                    @if ($headerPixabayImages)
                                        <div class="row mt-3">
                                            @foreach ($headerPixabayImages as $index => $image)
                                                <div class="col-md-3 text-center">
                                                    <img src="{{ $image['previewURL'] }}" alt="Header Image {{ $index }}" class="img-thumbnail" style="max-width: 100px;">
                                                    <button wire:click="selectHeaderImage({{ $index }})" class="btn btn-sm btn-primary mt-2">Auswählen</button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if ($header_image_path)
                                        <div class="mt-3 text-center">
                                            <img src="{{ asset('storage/' . $header_image_path) }}" alt="Ausgewähltes Header Bild" class="img-thumbnail" style="max-width: 150px;">
                                            <button wire:click="deleteHeaderImage" class="btn btn-sm btn-danger mt-2">
                                                <i class="ti ti-trash"></i> Löschen
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Country Text -->
                        <div class="mb-3">
                            <livewire:jodit-text-editor
                                wire:model.live="country_text"
                                :config="[
                                    'buttons' => [
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strikeThrough',
                                        '|',
                                        'left',
                                        'center',
                                        'right',
                                        '|',
                                        'link',
                                        'image',
                                    ],
                                    'enter' => 'DIV',
                                    'cleanHTML' => [
                                        'fillEmptyParagraph' => false,
                                        'removeEmptyNodes' => true,
                                        'denyTags' => ['script', 'iframe'],
                                    ],
                                    'defaultMode' => '1',
                                    'placeholder' => 'Geben Sie hier den Text ein...',
                                    'spellcheck' => true,
                                ]"
                            />
                        </div>

                        <!-- Custom Images Toggle -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input wire:model.change="custom_images" type="checkbox" id="custom_images" class="form-check-input" {{ $custom_images ? 'checked' : '' }}>
                                <label for="custom_images" class="form-check-label">Enable Custom Images</label>
                                <small class="text-muted d-block">Use custom image uploads instead of Pixabay images</small>
                            </div>
                        </div>

                        @if ($custom_images)
                            <!-- Bilder-Upload (max. 3 Bilder) -->
                            <div class="mb-4">
                                <label for="newImages" class="form-label">Bilder hochladen (max. 3)</label>
                                <input type="file" wire:model="newImages" class="form-control" multiple>
                                @error('newImages.*') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Zeigt hochgeladene oder gespeicherte Bilder -->
                            <div class="row mt-3">
                                @foreach (range(1, 3) as $i)
                                    <div class="col-md-4 text-center">
                                        @php
                                            $imageField = "image{$i}_path";
                                        @endphp

                                        @if (!empty($$imageField))
                                            <img src="{{ asset('storage/' . $$imageField) }}" alt="Custom Image {{ $i }}" class="img-thumbnail" style="max-width: 150px;">
                                            <button wire:click="deleteImage({{ $i }})" class="btn btn-sm btn-danger mt-2">
                                                <i class="ti ti-trash"></i> Löschen
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Zeigt Vorschau für neu hochgeladene Bilder -->
                            <div class="row mt-3">
                                @foreach ($newImages as $index => $image)
                                    <div class="col-md-4 text-center">
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview {{ $index }}" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Zeigt gespeicherte Pixabay-Bilder -->
                            <div class="row">
                                @foreach (range(1, 3) as $i)
                                    <div class="col-md-4 text-center">
                                        @php
                                            $imageField = "image{$i}_path";
                                        @endphp

                                        @if (!empty($$imageField))
                                            <img src="{{ asset('storage/' . $$imageField) }}" alt="Pixabay Image" class="img-thumbnail" style="max-width: 150px;">
                                            <button wire:click="deleteImage({{ $i }})" class="btn btn-sm btn-danger mt-2">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pixabay-Suche -->
                            <div class="mb-3">
                                <label for="searchKeyword" class="form-label">Search Images on Pixabay</label>
                                <div class="input-group">
                                    <input wire:model="searchKeyword" type="text" id="searchKeyword" class="form-control" placeholder="Enter keyword">
                                    <button wire:click="fetchImagesFromPixabay" class="btn btn-outline-secondary">Search</button>
                                </div>
                            </div>

                            <!-- Pixabay-Bilder auswählen -->
                            @if ($pixabayImages)
                                <div class="row">
                                    <h5 class="text-center">Pixabay Images</h5>
                                    @foreach ($pixabayImages as $index => $image)
                                        <div class="col-md-3 text-center">
                                            <img src="{{ $image['previewURL'] }}" alt="Pixabay Image {{ $index }}" class="img-thumbnail" style="max-width: 100px;">
                                            <button wire:click="selectPixabayImage({{ $index }})" class="btn btn-sm btn-primary mt-2">Select</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button wire:click="save" class="btn btn-primary">Save</button>
                        <button wire:click="resetInputFields" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('styles')

    @endpush


</div>
