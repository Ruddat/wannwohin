@extends('layouts.main')

@section('content')
<div class="container">
    <h1>Länder in @autotranslate($continent->title, app()->getLocale())</h1>
    <ul>
        @forelse ($countries as $country)
            <li>
                <strong>{{ $country->title }}</strong> ({{ $country->country_code }}) - {{ $country->capital }}
            </li>
        @empty
            <li>Keine Länder gefunden.</li>
        @endforelse
    </ul>
</div>
@endsection
