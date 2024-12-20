@extends('layouts.main')
@section('content')
    <div class="container pt-5-5">
        <form action="{{route('detail_search_result')}}" name="details_search" id="details_search_form" method="get">
{{--        <form action="https://www.wann-wohin.de/detailsuche" name="details_search" id="details_search_form" method="get">--}}
            @include('pages.detailSearch.general_info')
            @include('pages.detailSearch.continents')
            @include('pages.detailSearch.options')
            @include('pages.detailSearch.destination')
            @include('pages.detailSearch.activities')
            @include('pages.detailSearch.climate')
            <div class="d-flex mb-4">
                <button type="submit" id="submit" class="ms-auto bg-warning text-color-black mt-3 btn py-2 px-1 m-3"><i class="fas fa-search me-2"></i><span class="refresh_details_search_result">{{$total_locations}}</span> Ergebnisse
                    anzeigen
                </button>
            </div>
        </form>

        </div>
</div>
@endsection
