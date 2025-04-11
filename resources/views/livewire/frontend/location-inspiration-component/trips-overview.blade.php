<section class="py-5 bg-light border-top">
    <div class="container">
        <h2 class="mb-5 text-center">
            <i class="fas fa-map-marked-alt text-warning me-2"></i>
            Entdecke unsere empfohlenen Trips
        </h2>

        <div class="row justify-content-center">
            @foreach($trips as $trip)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="flip-card h-100 shadow-sm">
                        <div class="flip-card-inner rounded">
                            {{-- Vorderseite --}}
                            <div class="flip-card-front p-4 rounded text-dark">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                        <span class="fw-semibold small">{{ $trip->main_location }}</span>
                                    </div>
                                    <span class="badge bg-white text-dark shadow-sm">
                                        {{ count($trip->days[0]['activities'] ?? []) }} Aktivitäten
                                    </span>
                                </div>

                                @if(!empty($trip->days[0]['activities'][0]['image']))
                                    <img src="{{ $trip->days[0]['activities'][0]['image'] }}"
                                         class="img-fluid rounded mb-3"
                                         alt="{{ $trip->name }}">
                                @endif

                                <h5 class="fw-bold">{{ $trip->name }}</h5>
                                <ul class="ps-3 mt-2 small">
                                    @foreach(collect($trip->days)->first()['activities'] ?? [] as $activity)
                                        <li>{{ $activity['title'] }}</li>
                                        @break($loop->index === 1)
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Rückseite --}}
                            <div class="flip-card-back p-4 bg-white rounded d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between text-muted small mb-2">
                                        <span><i class="fas fa-clock me-1"></i> {{ $trip->duration ?? '3' }} Tage</span>
                                        <span><i class="fas fa-euro-sign me-1"></i> {{ $trip->price ?? 'ab 299' }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <span class="badge bg-info text-white me-1">Erlebnis</span>
                                        <span class="badge bg-success text-white">Outdoor</span>
                                    </div>

                                    <p class="text-muted small mb-3">{{ $trip->description }}</p>
                                </div>
                                <a href="#" class="btn btn-warning btn-sm w-100 shadow-sm">
                                    <i class="fas fa-arrow-right me-1"></i> Details ansehen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Flip-Card Styles --}}
        <style>
            .flip-card {
                perspective: 1200px;
                height: 100%;
                border-radius: 12px;
                cursor: pointer;
            }

            .flip-card-inner {
                position: relative;
                width: 100%;
                height: 100%;
                transition: transform 0.6s ease-in-out;
                transform-style: preserve-3d;
            }

            .flip-card.hover .flip-card-inner:hover {
                transform: rotateY(180deg);
            }

            .flip-card.clicked .flip-card-inner.flipped {
                transform: rotateY(180deg);
            }

            .flip-card-front, .flip-card-back {
                width: 100%;
                height: 100%;
                backface-visibility: hidden;
                border-radius: 12px;
                overflow: hidden;
            }

            .flip-card-front {
                background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            }

            .flip-card-back {
                background: #fff;
                transform: rotateY(180deg);
                position: absolute;
                top: 0;
                left: 0;
            }

            .flip-card img {
                object-fit: cover;
                height: 160px;
                width: 100%;
                border-radius: 8px;
            }

            .badge {
                font-size: 0.7rem;
                font-weight: 600;
            }

            h5 {
                font-family: 'Segoe UI', sans-serif;
            }
        </style>

        {{-- Flip-Card JS: Desktop = Hover, Touch = Click --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
                document.querySelectorAll('.flip-card').forEach(card => {
                    const inner = card.querySelector('.flip-card-inner');
                    if (isTouch) {
                        card.classList.add('clicked');
                        card.addEventListener('click', () => inner.classList.toggle('flipped'));
                    } else {
                        card.classList.add('hover');
                    }
                });
            });
        </script>
    </div>
</section>
