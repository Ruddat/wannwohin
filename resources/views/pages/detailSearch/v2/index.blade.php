{{-- resources/views/pages/detailSearch/v2/index.blade.php --}}
@extends('layouts.main')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- Sidebar mit Filtern --}}
        <div class="col-lg-3">
            @include('pages.detailSearch.v2.partials.filters', [
                'options' => $filterOptions,
                'appliedFilters' => $appliedFilters
            ])




    </div>

    <livewire:frontend.search.detail-search-v2
    :initial-continents="[1, 3, 4]"
    :initial-country="request('country')"
    :show-header="true"
/>


        {{-- Hauptinhalt --}}
        <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <span id="resultCount">{{ $initialCount }}</span>
                            Reiseziele gefunden
                        </h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" id="resetFilters">
                                <i class="fas fa-redo"></i> Zurücksetzen
                            </button>
                            <a href="{{ route('detail_search.v2.results', $appliedFilters) }}"
                               class="btn btn-primary">
                                Alle Ergebnisse anzeigen
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Vorschau --}}
            <div id="previewResults" class="row">
                {{-- AJAX geladen --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
class DetailSearchV2 {
    constructor() {
        this.form = document.getElementById('detailSearchForm');
        this.initEventListeners();
        this.loadPreview();
    }

    initEventListeners() {
        // Alle Filter-Inputs
        document.querySelectorAll('.filter-input').forEach(input => {
            input.addEventListener('change', () => this.updateResults());
        });

        // Reset Button
        document.getElementById('resetFilters').addEventListener('click', () => {
            this.form.reset();
            this.updateResults();
        });
    }

    updateResults() {
        const formData = new FormData(this.form);
        const params = new URLSearchParams();

        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }

        // API Call für Count und Preview
        fetch(`{{ route('detail_search.v2.index') }}?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('resultCount').textContent = data.count;
            document.getElementById('previewResults').innerHTML = data.previewHtml;
        })
        .catch(error => console.error('Error:', error));
    }

    loadPreview() {
        // Initiale Preview laden
        this.updateResults();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DetailSearchV2();
});
</script>
@endpush
