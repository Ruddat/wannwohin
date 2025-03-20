@extends('raadmin.layout.master')

@section('main-content')



<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Locations</h3>
            <div class="ms-auto">
                <a href="{{ route('location-manager.locations.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add New Location
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif


            @livewire('backend.location.location-component')


        </div>
    </div>
</div>


@endsection
