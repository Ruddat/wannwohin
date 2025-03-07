@extends('layouts.main')

@section('content')
    <div class="container-xl mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h1 class="card-title mb-0">{{ $content['title'] }}</h1>
                    </div>
                    <div class="card-body">
                        {!! $content['body'] !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('seo')
    <!-- Dynamische SEO-Daten -->
    @if (isset($seo))
        <title>{{ $seo['title'] }}</title>
        <meta name="description" content="{{ $seo['description'] }}">
        <link rel="canonical" href="{{ $seo['canonical'] }}">
        <meta name="keywords" content="{{ implode(', ', $seo['keywords']['tags'] ?? []) }}">
        @if ($seo['image'])
            <meta property="og:image" content="{{ $seo['image'] }}">
        @endif
    @endif

    <!-- Strukturierte Daten -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "{{ $seo['title'] }}",
        "description": "{{ $seo['description'] }}",
        "url": "{{ $seo['canonical'] }}"
    }
    </script>
@endsection
