@extends('raadmin.layout.master')

@section('content')
<div class="container mt-5">
    <h2>Standort-Texte importieren</h2>

    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('location-text-import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Excel-Datei hochladen:</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Import starten</button>
    </form>
</div>
@endsection
