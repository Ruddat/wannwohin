@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">{{ $location->title }}</h1>
            <p>{{ $location->description ?? 'Beschreibung nicht verfügbar' }}</p>

            @if($main_image_path)
                <img src="{{ $main_image_path }}" class="img-fluid rounded mb-4" alt="{{ $location->title }}">
            @endif
        </div>
    </div>
</div>
@endsection
dfgdfgdfgdfg
