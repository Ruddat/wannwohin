@extends('raadmin.layout.master')

@section('main-content')

<div class="page-body">
    <div class="container-xl d-flex flex-column justify-content-center">

        @livewire('backend.image-imports.import-startpage-images')

        @livewire('backend.image-imports.location-main-img')

    </div>
  </div>
@endsection

