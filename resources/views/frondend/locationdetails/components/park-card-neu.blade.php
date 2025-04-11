<!-- park-card.blade.php -->
<div class="col-12 col-sm-6 col-md-4 mb-4">
    <div class="park-card shadow-sm">
        <div class="park-card__inner">
            {{-- Vorderseite --}}
            <div class="park-card__front">
                <div class="park-card__image" style="background-image: url('{{ asset($park->logo_url) }}');"></div>
                <div class="park-card__content">
                    <h5 class="park-card__title">{{ $park->name }}</h5>
                    <p class="park-card__description text-muted small">
                        {{ $park->short_description ?? 'Ein Freizeitpark mit unvergesslichen Momenten.' }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-success">{{ $park->category ?? 'Outdoor' }}</span>
                        <span class="text-warning small">★★★★☆ ({{ $park->rating ?? '4.5' }})</span>
                    </div>
                </div>
            </div>

            {{-- Rückseite --}}
            <div class="park-card__back">
                <div class="park-card__content">
                    <h6 class="fw-bold mb-2">Highlights</h6>
                    <ul class="small ps-3">
                        <li>Über 40 Attraktionen</li>
                        <li>Familienfreundlich</li>
                        <li>Saisonale Events</li>
                    </ul>

                    @if ($park->embed_code)
                        <div class="mt-3">{!! $park->embed_code !!}</div>
                    @elseif ($park->video_url)
                        @php
                            $url = $park->video_url;
                            if (str_contains($url, 'youtube.com')) {
                                $id = explode('v=', $url)[1] ?? '';
                                $id = strtok($id, '&');
                                $embed = "https://www.youtube.com/embed/$id";
                            } elseif (str_contains($url, 'vimeo.com')) {
                                $id = basename($url);
                                $embed = "https://player.vimeo.com/video/$id";
                            } else {
                                $embed = $url;
                            }
                        @endphp
                        <div class="mt-3">
                            <iframe src="{{ $embed }}" width="100%" height="180" frameborder="0" allowfullscreen></iframe>
                        </div>
                    @endif

                    <a href="#" class="btn btn-warning btn-sm w-100 mt-3">
                        <i class="fas fa-ticket-alt me-1"></i> Jetzt entdecken
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.park-card {
    perspective: 1000px;
    height: 100%;
}
.park-card__inner {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.6s;
}
</style>
