<div class="page-body">
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="container-xl">
        <div class="row row-cards">
            <!-- Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Countries</h3>
                    </div>

                    <div class="card-body border-bottom py-3">
                        <div class="row">
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
                                    <input wire:model.live="search" type="text" id="search" class="form-control"
                                        placeholder="Name oder Code...">
                                </div>
                            </div>
                            <!-- Export-Button -->
                            <div class="col-md-2 col-sm-6 mb-3 text-end">
                                <label class="form-label d-block">Export</label>
                                <button wire:click="exportToExcel" class="btn btn-success">
                                    <i class="ti ti-download"></i> Excel herunterladen
                                </button>
                            </div>
                        </div>
                    </div>




                    <div class="table-responsive">
                        <table class="table table-hover card-table table-vcenter">
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
                                        <!-- Thumbnail-Spalte -->
                                        <td>
                                            <img src="{{ $country->thumbnail }}" alt="{{ $country->title }}"
                                                class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>{{ $country->title }}</td>
                                        <td>{{ $country->continent->title ?? 'N/A' }}</td>
                                        <td>{{ $country->country_code }}</td>
                                        <td>{{ $country->population ?? 'N/A' }}</td>
                                        <td>
                                            <button wire:click="toggleStatus({{ $country->id }})"
                                                class="btn btn-sm @if ($country->status === 'active') btn-success @elseif ($country->status === 'pending') btn-warning @else btn-secondary @endif">
                                                {{ ucfirst($country->status) }}
                                            </button>
                                        </td>
                                        <td>
                                            <button wire:click="edit({{ $country->id }})"
                                                class="btn btn-icon btn-primary btn-sm" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button wire:click="delete({{ $country->id }})"
                                                class="btn btn-icon btn-danger btn-sm" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No countries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $countries->links() }}
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

                        <form action="{{ route('countries.import') }}" method="POST" enctype="multipart/form-data"
                            class="bg-white p-4 rounded shadow">
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
    <div>
        @if ($editMode)
            <div class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5);">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $countryId ? 'Edit Country' : 'Create Country' }}</h5>
                            <button type="button" class="btn-close" wire:click="resetInputFields"></button>
                        </div>
                        <div class="modal-body">


                            <div class="row">
                                <div class="col-md-6 mb-3">
                            <!-- Title -->
                                <label for="title" class="form-label">Title</label>
                                <input wire:model="title" type="text" id="title" class="form-control"
                                    placeholder="Country Title">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                            <!-- Continent -->
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
                                    <!-- Country Code -->
                                    <label for="country_code" class="form-label">Country Code (ISO2)</label>
                                    <input wire:model="country_code" type="text" id="country_code"
                                        class="form-control" placeholder="Country Code (e.g., US)">
                                    @error('country_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <!-- Country ISO3 Code -->
                                    <label for="country_iso_3" class="form-label">Country ISO3 Code</label>
                                    <input wire:model="country_iso_3" type="text" id="country_iso_3"
                                        class="form-control" placeholder="Country ISO3 Code (e.g., USA)">
                                    @error('country_iso_3')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <!-- Currency Code -->
                            <div class="mb-3">
                                <label for="currency_code" class="form-label">Currency Code</label>
                                <input wire:model="currency_code" type="text" id="currency_code"
                                    class="form-control" placeholder="Currency Code (e.g., USD)">
                                @error('currency_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Currency Name -->
                            <div class="mb-3">
                                <label for="currency_name" class="form-label">Currency Name</label>
                                <input wire:model="currency_name" type="text" id="currency_name"
                                    class="form-control" placeholder="Currency Name (e.g., US Dollar)">
                                @error('currency_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Currency Conversion -->
                            <div class="mb-3">
                                <label for="currency_conversion" class="form-label">Currency Conversion</label>
                                <input wire:model="currency_conversion" type="text" id="currency_conversion"
                                    class="form-control" placeholder="Currency Conversion (e.g., 1 USD = 0.85 EUR)">
                                @error('currency_conversion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Population -->
                            <div class="mb-3">
                                <label for="population" class="form-label">Population</label>
                                <input wire:model="population" type="number" id="population" class="form-control"
                                    placeholder="Population">
                                @error('population')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Capital -->
                            <div class="mb-3">
                                <label for="capital" class="form-label">Capital</label>
                                <input wire:model="capital" type="text" id="capital" class="form-control"
                                    placeholder="Capital">
                                @error('capital')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Population Capital -->
                            <div class="mb-3">
                                <label for="population_capital" class="form-label">Population Capital</label>
                                <input wire:model="population_capital" type="number" id="population_capital"
                                    class="form-control" placeholder="Population Capital">
                                @error('population_capital')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Area -->
                            <div class="mb-3">
                                <label for="area" class="form-label">Area (in km²)</label>
                                <input wire:model="area" type="number" id="area" class="form-control"
                                    placeholder="Area">
                                @error('area')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Official Language -->
                            <div class="mb-3">
                                <label for="official_language" class="form-label">Official Language</label>
                                <input wire:model="official_language" type="text" id="official_language"
                                    class="form-control" placeholder="Official Language">
                                @error('official_language')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Language EZMZ -->
                            <div class="mb-3">
                                <label for="language_ezmz" class="form-label">Language EZMZ</label>
                                <input wire:model="language_ezmz" type="text" id="language_ezmz"
                                    class="form-control" placeholder="Language EZMZ">
                                @error('language_ezmz')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- BSP in USD -->
                            <div class="mb-3">
                                <label for="bsp_in_USD" class="form-label">BSP in USD</label>
                                <input wire:model="bsp_in_USD" type="number" id="bsp_in_USD" class="form-control"
                                    placeholder="BSP in USD">
                                @error('bsp_in_USD')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Life Expectancy (Men) -->
                            <div class="mb-3">
                                <label for="life_expectancy_m" class="form-label">Life Expectancy (Men)</label>
                                <input wire:model="life_expectancy_m" type="number" step="0.1"
                                    id="life_expectancy_m" class="form-control" placeholder="Life Expectancy (Men)">
                                @error('life_expectancy_m')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Life Expectancy (Women) -->
                            <div class="mb-3">
                                <label for="life_expectancy_w" class="form-label">Life Expectancy (Women)</label>
                                <input wire:model="life_expectancy_w" type="number" step="0.1"
                                    id="life_expectancy_w" class="form-control"
                                    placeholder="Life Expectancy (Women)">
                                @error('life_expectancy_w')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Population Density -->
                            <div class="mb-3">
                                <label for="population_density" class="form-label">Population Density</label>
                                <input wire:model="population_density" type="number" step="0.1"
                                    id="population_density" class="form-control" placeholder="Population Density">
                                @error('population_density')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Visum benötigt -->
                            <div class="mb-3">
                                <label for="country_visum_needed" class="form-label">Visum benötigt</label>
                                <select wire:model="country_visum_needed" id="country_visum_needed"
                                    class="form-select">
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
                                <input wire:model="country_visum_max_time" type="text" id="country_visum_max_time"
                                    class="form-control" placeholder="Maximale Visumdauer">
                                @error('country_visum_max_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Preis-Tendenz -->
                            <div class="mb-3">
                                <label for="price_tendency" class="form-label">Preis-Tendenz</label>
                                <input wire:model="price_tendency" type="text" id="price_tendency"
                                    class="form-control" placeholder="Preis-Tendenz">
                                @error('price_tendency')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="country_code" class="form-label">Country Code (ISO2)</label>
                                <div class="input-group">
                                    <input wire:model="country_code" type="text" id="country_code" class="form-control"
                                        placeholder="Country Code (z.B. US)">
                                    <button wire:click="fetchCountryData" class="btn btn-primary">
                                        <i class="ti ti-download"></i> Automatisch füllen
                                    </button>
                                </div>
                                @error('country_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <!-- Country Text -->
                            <div class="mb-3">
                                <livewire:jodit-text-editor wire:model.live="country_text" :buttons="[
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
                                ]" />
                            </div>

<!-- Custom Images Toggle -->
<div class="mb-3">
    <label for="custom_images" class="form-label">Enable Custom Images</label>
    <input wire:model.change="custom_images" type="checkbox" id="custom_images" {{ $custom_images ? 'checked' : '' }}>
    <span>Use custom image uploads instead of Pixabay images</span>
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
                    <button wire:click="selectPixabayImage({{ $index }})" class="btn btn-sm btn-primary mt-2">
                        Select
                    </button>
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



    </div>

    <!-- Include Jodit CSS Styling -->
    <link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">

    <!-- Include the Jodit JS Library -->
    <script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>

</div>
</div>
