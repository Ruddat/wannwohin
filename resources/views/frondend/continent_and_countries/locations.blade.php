@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">
        <div role="main" class="main">
            @include('frondend.continent_and_countries.section.locations.locations_facts')
            @include('frondend.continent_and_countries.section.locations.locations_table')
        </div>

    </div>
@endsection

