@extends('layouts.main')

@section('content')
<div class="container pt-5-5">
    <!-- Formular für die Detail-Suche -->
    <form action="{{ route('detail_search_result') }}" method="get">
        <!-- Allgemeine Informationen -->
        @include('pages.detailSearch.general_info', [
            'countries' => $countries,
            'ranges' => $ranges,
            'climate_lnam' => $climate_lnam
        ])

        @include('pages.detailSearch.continents', ['continents' => $continents])

        @include('frondend.detailSearch.sections.options', ['countries' => $countries, 'languages' => $languages, 'ranges' => $ranges])

        @include('frondend.detailSearch.sections.climate')

        <!-- Submit Button -->
        <div class="d-flex justify-content-end mb-4">
            <button
                type="submit"
                class="btn btn-warning text-black btn-lg px-4 py-2">
                <i class="fas fa-search me-2"></i> Ergebnisse anzeigen
            </button>
        </div>
    </form>
</div>
@endsection
<style>
    
</style>
