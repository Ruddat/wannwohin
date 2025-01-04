@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">
        <div role="main" class="main">
            @include('frondend.continent_and_countries.section.facts')
            @include('frondend.continent_and_countries.section.countries')
        </div>

    </div>
@endsection

