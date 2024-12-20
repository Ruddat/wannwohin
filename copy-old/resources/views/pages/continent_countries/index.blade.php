@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">
        <div role="main" class="main">
            @include('pages.continent_countries.facts')
            @include('pages.continent_countries.countries')
        </div>

    </div>
@endsection

