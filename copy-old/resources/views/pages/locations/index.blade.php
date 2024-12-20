@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">
        <div role="main" class="main">
            @include('pages.locations.facts')
            @include('pages.locations.locations')
        </div>

    </div>
@endsection

