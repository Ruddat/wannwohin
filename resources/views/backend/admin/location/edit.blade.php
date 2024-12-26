@extends('backend.layouts.main')

@section('main-content')
<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Location</h3>
            <div class="ms-auto">
                <a href="{{ route('location-manager.locations.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('location-manager.locations.update', $location->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- General Information -->
                <h4 class="mb-3">General Information</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ $location->title }}">
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alias</label>
                        <input type="text" name="alias" class="form-control @error('alias') is-invalid @enderror" value="{{ $location->alias }}">
                        @error('alias')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">IATA Code</label>
                        <input type="text" name="iata_code" class="form-control @error('iata_code') is-invalid @enderror" value="{{ $location->iata_code }}">
                        @error('iata_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Flight Hours</label>
                        <input type="number" step="0.1" name="flight_hours" class="form-control @error('flight_hours') is-invalid @enderror" value="{{ $location->flight_hours }}">
                        @error('flight_hours')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Stop Overs</label>
                        <input type="number" name="stop_over" class="form-control @error('stop_over') is-invalid @enderror" value="{{ $location->stop_over }}">
                        @error('stop_over')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Location Details -->
                <h4 class="mb-3">Location Details</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="lat" class="form-control @error('lat') is-invalid @enderror" value="{{ $location->lat }}">
                        @error('lat')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="lon" class="form-control @error('lon') is-invalid @enderror" value="{{ $location->lon }}">
                        @error('lon')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Categories -->
                <h4 class="mb-3">Categories</h4>
                <div class="row">
                    @php
                        $categories = [
                            'list_beach' => 'Beach',
                            'list_citytravel' => 'City Travel',
                            'list_sports' => 'Sports',
                            'list_island' => 'Island',
                            'list_culture' => 'Culture',
                            'list_nature' => 'Nature',
                            'list_watersport' => 'Watersport',
                            'list_wintersport' => 'Winter Sports',
                            'list_mountainsport' => 'Mountain Sports',
                            'list_biking' => 'Biking',
                            'list_fishing' => 'Fishing',
                            'list_amusement_park' => 'Amusement Park',
                            'list_water_park' => 'Water Park',
                            'list_animal_park' => 'Animal Park',
                        ];
                    @endphp
                    @foreach($categories as $field => $label)
                        <div class="col-md-3 mb-3">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="{{ $field }}" {{ $location->$field ? 'checked' : '' }}>
                                <span class="form-check-label">{{ $label }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Additional Information -->
                <h4 class="mb-3">Additional Information</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Best Travel Time</label>
                        <input type="text" name="best_traveltime" class="form-control @error('best_traveltime') is-invalid @enderror" value="{{ $location->best_traveltime }}">
                        @error('best_traveltime')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Time Zone</label>
                        <input type="text" name="time_zone" class="form-control @error('time_zone') is-invalid @enderror" value="{{ $location->time_zone }}">
                        @error('time_zone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Descriptions -->
                <h4 class="mb-3">Descriptions</h4>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="text_short" rows="3" class="form-control @error('text_short') is-invalid @enderror">{{ $location->text_short }}</textarea>
                        @error('text_short')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">What to Do</label>
                        <textarea name="text_what_to_do" rows="3" class="form-control @error('text_what_to_do') is-invalid @enderror">{{ $location->text_what_to_do }}</textarea>
                        @error('text_what_to_do')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Save and Cancel -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('location-manager.locations.index') }}" class="btn btn-secondary me-2">
                        <i class="ti ti-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('continent').addEventListener('change', function() {
        const continentId = this.value;
        const countrySelect = document.getElementById('country');

        countrySelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/api/countries-by-continent/${continentId}`)
            .then(response => response.json())
            .then(data => {
                countrySelect.innerHTML = '<option value="">Select Country</option>';
                data.forEach(country => {
                    countrySelect.innerHTML += `<option value="${country.id}">${country.title}</option>`;
                });
            });
    });
</script>
@endpush
