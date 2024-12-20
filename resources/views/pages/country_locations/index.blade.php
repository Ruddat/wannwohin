@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">
        <div role="main" class="main">
            @include('pages.country_locations.facts')
            @include('pages.country_locations.locations')
        </div>

    </div>
@endsection

