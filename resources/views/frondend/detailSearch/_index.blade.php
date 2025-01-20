@extends('layouts.main')

@section('content')
<div class="container pt-5-5">
    <!-- Formular für die Detail-Suche -->
    <form id="detailSearchForm" action="{{ route('detail_search_result') }}" method="get">
        <!-- Allgemeine Informationen -->
        @include('pages.detailSearch.general_info', [
            'countries' => $countries,
            'ranges' => $ranges,
            'climate_lnam' => $climate_lnam
        ])

        @include('pages.detailSearch.continents', ['continents' => $continents])

        @include('frondend.detailSearch.sections.options', [
            'countries' => $countries,
            'languages' => $languages,
            'ranges' => $ranges,
            'price_tendencies' => $price_tendencies
            ])



    @include('frondend.detailSearch.sections.destination', [
        'flightDuration' => $flightDuration,
        'Destinations' => $Destinations
    ])

@include('frondend.detailSearch.sections.activities')

        @include('frondend.detailSearch.sections.climate')

        <!-- Submit Button -->
        <div class="d-flex justify-content-end mb-4">
            <button
            type="submit"
            id="searchButton"
            class="btn btn-warning text-black btn-lg px-4 py-2">
            <i class="fas fa-search me-2"></i>
            Ergebnisse anzeigen (<span id="locationCount">{{ $total_locations }}</span>)
        </button>
        </div>
    </form>



</div>
@endsection
<style>

</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('detailSearchForm');
    const filterInputs = document.querySelectorAll('.filter-input');
    const locationCount = document.getElementById('locationCount');

    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            const formData = new FormData(form);
            const queryString = new URLSearchParams(formData).toString();

            fetch(`${form.action}?${queryString}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                // Nur die Anzahl der Locations aktualisieren
                locationCount.textContent = data.count;
            })
            .catch(error => {
                console.error('Fehler beim Abrufen der Daten:', error);
            });
        });
    });
});


</script>
