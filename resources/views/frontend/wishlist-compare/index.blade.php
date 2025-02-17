@extends('layouts.main')

@section('content')
    <div class="container">
        <h1>Vergleich</h1>

        {{-- Livewire-Komponente mit den IDs --}}

@livewire('frontend.wishlist-select.wishlist-compare-component', ['slugs' => implode('-', $locations->pluck('slug')->toArray())])
    </div>
@endsection
