@extends('backend.layouts.main')

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

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Alias</th>
                            <th>IATA Code</th>
                            <th>Flight Hours</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                            <tr>
                                <td>{{ $location->id }}</td>
                                <td>{{ $location->title }}</td>
                                <td>{{ $location->alias }}</td>
                                <td>{{ $location->iata_code }}</td>
                                <td>{{ $location->flight_hours }}</td>
                                <td class="text-end">
                                    <a href="{{ route('location-manager.locations.edit', $location->id) }}" class="btn btn-warning btn-sm">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('location-manager.locations.destroy', $location->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            <i class="ti ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No locations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
