@extends('backend.layouts.main')

@section('main-content')
<div class="container">
    <h1>Create New Location</h1>
    <form action="{{ route('location-manager.locations.store') }}" method="POST">
        @csrf

        <!-- Title, Alias, IATA Code -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Alias</label>
                <input type="text" name="alias" class="form-control @error('alias') is-invalid @enderror" value="{{ old('alias') }}">
                @error('alias')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">IATA Code</label>
                <input type="text" name="iata_code" class="form-control @error('iata_code') is-invalid @enderror" value="{{ old('iata_code') }}">
                @error('iata_code')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Continent, Country -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Continent</label>
                <select name="continent_id" id="continent" class="form-select @error('continent_id') is-invalid @enderror">
                    <option value="">Select Continent</option>
                    @foreach($continents as $continent)
                        <option value="{{ $continent->id }}" {{ old('continent_id', $location->continent_id ?? '') == $continent->id ? 'selected' : '' }}>
                            {{ $continent->title }}
                        </option>
                    @endforeach
                </select>
                @error('continent_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Country</label>
                <select name="country_id" id="country" class="form-select @error('country_id') is-invalid @enderror">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id', $location->country_id ?? '') == $country->id ? 'selected' : '' }}>
                            {{ $country->title }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Coordinates -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Latitude</label>
                <input type="text" name="lat" class="form-control @error('lat') is-invalid @enderror" value="{{ old('lat') }}">
                @error('lat')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Longitude</label>
                <input type="text" name="lon" class="form-control @error('lon') is-invalid @enderror" value="{{ old('lon') }}">
                @error('lon')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Flight and Distance -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Flight Hours</label>
                <input type="text" name="flight_hours" class="form-control @error('flight_hours') is-invalid @enderror" value="{{ old('flight_hours') }}">
                @error('flight_hours')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Distance from FRA</label>
                <input type="text" name="dist_from_FRA" class="form-control @error('dist_from_FRA') is-invalid @enderror" value="{{ old('dist_from_FRA') }}">
                @error('dist_from_FRA')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Distance Type</label>
                <input type="text" name="dist_type" class="form-control @error('dist_type') is-invalid @enderror" value="{{ old('dist_type') }}">
                @error('dist_type')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Features -->
        <div class="mb-3">
            <label class="form-label">Features</label>
            <div class="row">
                @foreach(['list_beach' => 'Beach', 'list_citytravel' => 'City Travel', 'list_sports' => 'Sports', 'list_island' => 'Island'] as $field => $label)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="{{ $field }}" value="1" {{ old($field) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $label }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Descriptions -->
        <div class="mb-3">
            <label class="form-label">Short Description</label>
            <textarea name="text_short" class="form-control @error('text_short') is-invalid @enderror">{{ old('text_short') }}</textarea>
            @error('text_short')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Detailed Description</label>
            <textarea name="text_location_climate" class="form-control @error('text_location_climate') is-invalid @enderror">{{ old('text_location_climate') }}</textarea>
            @error('text_location_climate')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Pricing -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Flight Price</label>
                <input type="number" name="price_flight" class="form-control @error('price_flight') is-invalid @enderror" value="{{ old('price_flight') }}">
                @error('price_flight')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Hotel Price</label>
                <input type="number" name="price_hotel" class="form-control @error('price_hotel') is-invalid @enderror" value="{{ old('price_hotel') }}">
                @error('price_hotel')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Rental Price</label>
                <input type="number" name="price_rental" class="form-control @error('price_rental') is-invalid @enderror" value="{{ old('price_rental') }}">
                @error('price_rental')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-end">
            <a href="{{ route('location-manager.locations.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
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
