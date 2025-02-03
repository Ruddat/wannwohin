<div class="card p-4 shadow-sm">
    <h3 class="mb-4">Tags & Reisezeiten</h3>

    <!-- Tag Auswahl -->
    <div class="mb-4">
        <label class="form-label fw-bold">Tags</label>
        <div class="row g-2">
            @foreach($tags as $key => $value)
                <div class="col-6 col-sm-4">
                    <label class="form-check d-flex flex-column align-items-center p-3 border rounded shadow-sm w-100">
                        @php
                        $icons = [
                            'list_beach' => 'fa-solid fa-umbrella-beach',
                            'list_citytravel' => 'fa-solid fa-city',
                            'list_sports' => 'fa-solid fa-basketball-ball',
                            'list_island' => 'fa-solid fa-mountain-sun',
                            'list_culture' => 'fa-solid fa-landmark',
                            'list_nature' => 'fa-solid fa-leaf',
                            'list_watersport' => 'fa-solid fa-water',
                            'list_wintersport' => 'fa-solid fa-snowflake',
                            'list_mountainsport' => 'fa-solid fa-mountain',
                            'list_biking' => 'fa-solid fa-bicycle',
                            'list_fishing' => 'fa-solid fa-fish',
                            'list_amusement_park' => 'fa-solid fa-ticket-alt',
                            'list_water_park' => 'fa-solid fa-swimmer',
                            'list_animal_park' => 'fa-solid fa-paw',
                        ];
                        $icon = $icons[$key] ?? 'fa-solid fa-check-square';
                    @endphp


                        <i class="{{ $icons[$key] ?? 'fa-solid fa-check-square' }} text-primary mb-2" style="font-size: 1.5rem;"></i>
                        <input type="checkbox" class="form-check-input me-2" wire:model="tags.{{ $key }}" {{ $value ? 'checked' : '' }}>
                        <span class="form-check-label mt-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Beste Reisezeit Auswahl -->
    <div class="mb-4">
        <label class="form-label fw-bold">Beste Reisezeit</label>
        <div class="row g-2">
            @foreach ($travel_time_options as $option)
                <div class="col-6 col-sm-2">
                    <label class="form-imagecheck d-flex flex-column align-items-center p-2 border rounded shadow-sm w-100">
                        <i class="fa-solid fa-calendar-days text-primary mb-2" style="font-size: 1.5rem;"></i>
                        <img src="{{ asset('img/best_travel_time/' . $option . '.png') }}" alt="{{ $option }}" class="form-imagecheck-image rounded mb-2">
                        <input type="checkbox" wire:model="best_traveltime" value="{{ $option }}" class="form-check-input">
                        <span class="mt-2">{{ $option }}</span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Beste Reisezeit Editor -->
    <div class="mb-4">
        <label class="form-label fw-bold">Beste Reisezeit Beschreibung</label>
        <livewire:jodit-text-editor wire:model.live="best_traveltime_text" :buttons="['bold', 'italic', 'underline', '|', 'left', 'center', 'right', '|', 'unorderedList', 'orderedList', '|', 'link', 'image']" />
    </div>

    <!-- Sport Beschreibung Editor -->
    <div class="mb-4">
        <label class="form-label fw-bold">Sport Beschreibung</label>
        <livewire:jodit-text-editor wire:model.live="text_sports" :buttons="['bold', 'italic', 'underline', '|', 'left', 'center', 'right', '|', 'unorderedList', 'orderedList', '|', 'link', 'image']" />
    </div>

    <!-- Freizeitpark Beschreibung Editor -->
    <div class="mb-4">
        <label class="form-label fw-bold">Freizeitpark Beschreibung</label>
        <livewire:jodit-text-editor wire:model.live="text_amusement_parks" :buttons="['bold', 'italic', 'underline', '|', 'left', 'center', 'right', '|', 'unorderedList', 'orderedList', '|', 'link', 'image']" />
    </div>

    <button class="btn btn-primary w-100" wire:click="updateTags">Speichern</button>

    @if (session()->has('success'))
        <div class="alert alert-success mt-3 text-center">
            {{ session('success') }}
        </div>
    @endif

<style>
    .form-check .form-check-input {
    float: left;
    margin-left: 0.5rem;
    }
</style>
</div>
