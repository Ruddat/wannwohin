@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">
        <div role="main" class="main">
            @include('pages.countries.facts')
            @include('pages.countries.countries')
        </div>

    </div>
@endsection

