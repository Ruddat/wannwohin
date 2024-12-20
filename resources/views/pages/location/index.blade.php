@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">

        <div role="main" class="main">
            @include('pages.location.sections.main')
            @include('pages.location.sections.experience')
{{--            @include('pages.location.sections.image_gallery')--}}
            @include('pages.location.sections.erleben')
            @if($location_image_gallery)
                @include('pages.location.sections.image_gallery')
            @endif
            @include('pages.location.sections.erleben_picture_modal')
            @include('pages.location.sections.google_map_modal')
        </div>

    </div>
@endsection

