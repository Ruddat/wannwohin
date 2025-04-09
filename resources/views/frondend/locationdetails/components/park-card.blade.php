<!-- resources/views/components/park-card.blade.php -->
<div class="col-12 col-sm-6 col-md-4">
    <div class="card shadow-md border-0 h-100 transform hover:-translate-y-2 hover:shadow-lg transition-all duration-300 bg-white rounded-xl overflow-hidden">
        <div class="card-header bg-gradient-to-r from-gray-50 to-gray-100 p-2">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center flex-wrap">
                @if ($item['park']->logo_url)
                    <img src="{{ asset($item['park']->logo_url) }}" alt="{{ $item['park']->name }} Logo" class="transition-transform duration-300 hover:scale-105">
                @else
                    <div class="w-8 h-8 bg-gray-200 rounded-full d-flex align-items-center justify-content-center"></div>
                @endif
                <div class="ms-sm-3 mt-2 mt-sm-0 text-center">
                    <h5 class="font-bold text-lg mb-0">{{ $item['park']->name }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body text-center p-4">
            @if ($item['park']->embed_code)
                <!-- Embed-Code direkt einbetten -->
                <div class="mt-3 relative">
                    <div class="video-frame rounded-lg overflow-hidden shadow-md border border-gray-200 transition-transform duration-300 hover:scale-105">
                        {!! $item['park']->embed_code !!}
                    </div>
                </div>
            @elseif ($item['park']->video_url)
                <!-- Fallback auf video_url -->
                <div class="mt-3 relative">
                    <div class="video-frame rounded-lg overflow-hidden shadow-md border border-gray-200 transition-transform duration-300 hover:scale-105">
                        @if (str_contains($item['park']->video_url, 'youtube.com'))
                            @php
                                $videoId = str_contains($item['park']->video_url, 'v=')
                                    ? explode('v=', $item['park']->video_url)[1]
                                    : basename($item['park']->video_url);
                                $videoId = strtok($videoId, '&');
                                $embedUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=0&mute=1";
                            @endphp
                            <iframe width="100%" height="180" src="{{ $embedUrl }}" frameborder="0" allowfullscreen></iframe>
                        @elseif (str_contains($item['park']->video_url, 'vimeo.com'))
                            @php
                                $videoId = basename($item['park']->video_url);
                                $embedUrl = "https://player.vimeo.com/video/{$videoId}?autoplay=0&muted=1";
                            @endphp
                            <iframe width="100%" height="180" src="{{ $embedUrl }}" frameborder="0" allowfullscreen></iframe>
                        @else
                            <video width="100%" height="180" controls class="rounded-lg">
                                <source src="{{ $item['park']->video_url }}" type="video/mp4">
                                Ihr Browser unterstützt das Video-Tag nicht.
                            </video>
                        @endif
                    </div>
                </div>
            @endif
            <div class="info-grid mb-4 text-gray-700">
                <div class="info-item flex justify-between items-center">
                    <span class="text-muted font-medium">Ort:</span>
                    <span>{{ $item['park']->country ?? 'Unbekannt' }}</span>
                </div>
                <div class="info-item flex justify-between items-center">
                    <span class="text-muted font-medium">Entfernung:</span>
                    <span>{{ round($item['park']->distance, 1) }} km</span>
                </div>
                <div class="info-item flex justify-between items-center">
                    <span class="text-muted font-medium">Coolness:</span>
                    <div class="coolness-bar w-32 h-3 bg-gray-200 rounded-full overflow-hidden relative">
                        <div class="h-full rounded-full transition-all duration-700 ease-out" style="width: {{ $item['coolness_score'] }}%; background: linear-gradient(45deg, #10b981, #3b82f6, #8b5cf6); box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-white mix-blend-difference">{{ $item['coolness_score'] }}</span>
                    </div>
                </div>
            </div>
            @if ($item['opening_times'])
                <div class="opening-times bg-gradient-to-r from-gray-50 to-gray-100 p-2 rounded-lg mb-3 transition-transform duration-300 hover:bg-gray-200 hover:shadow-md">
                    <div class="status-badge {{ $item['opening_times']['opened_today'] ? 'bg-emerald-500' : 'bg-rose-500' }} text-white font-medium px-2 py-1 rounded-full transition-transform duration-200 hover:scale-105">
                        {{ $item['opening_times']['opened_today'] ? 'Geöffnet' : 'Geschlossen' }}
                    </div>
                    <p class="mt-2 mb-0 text-sm text-gray-600">
                        <span class="font-medium">Öffnet:</span> {{ $item['opening_times']['open_from'] ?? 'N/A' }}<br>
                        <span class="font-medium">Schließt:</span> {{ $item['opening_times']['closed_from'] ?? 'N/A' }}
                    </p>
                </div>
            @endif
            @if (!empty($item['waiting_times']))
                <button class="btn btn-gradient waiting-times-btn mt-3 font-medium text-white rounded-full shadow-lg transition-all duration-300 hover:brightness-110 hover:shadow-xl"
                        data-park-name="{{ $item['park']->name }}"
                        data-waiting-times='@json($item['waiting_times'])'
                        data-last-updated="{{ $item['last_updated'] ?? 'N/A' }}"
                        data-bs-toggle="modal" data-bs-target="#waitingTimesModal">
                    Wartezeiten entdecken
                </button>
            @endif
        </div>
    </div>

    <style>
        .video-frame {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    height: 0;
    overflow: hidden;
}
.video-frame iframe,
.video-frame video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
    </style>
</div>


