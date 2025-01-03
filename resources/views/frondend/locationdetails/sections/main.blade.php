<section class="section section-no-border bg-color-light m-0 pb-0" style="background-color: #eaeff5 !important;">
    <div class="container" style="background-color: #eaeff5">
        <!-- Überschrift -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-uppercase">{{ $location->title }}: Alles Wichtige auf einen Blick</h2>
                <hr class="w-25 mx-auto" style="border: 2px solid #007bff;">
            </div>
        </div>

        <div class="row">
            <!-- Hauptbild -->
            <div class="col-lg-7 col-md-12">
                @php
                    $imagePaths = array_filter([
                        $location->text_pic1,
                        $location->text_pic2,
                        $location->text_pic3,
                    ]);
                    $randomImage = $imagePaths ? $imagePaths[array_rand($imagePaths)] : null;
                @endphp

                <div class="figure-img img-fluid custom-border position-relative"
                     style="background-repeat: no-repeat; background-size: cover; background-position: center;
                            background-image: url('{{ $randomImage ? asset($randomImage) : asset("img/placeholders/location-placeholder.jpg") }}');
                            height: 100%; min-height: 400px;">
                    <!-- Schicker Bildtext im unteren Bereich -->
                    <div class="position-absolute bottom-0 w-100 bg-opacity-75 bg-white text-dark p-3 rounded-top shadow-lg">
                        <p class="mb-0 text-center fw-bold">
                            {{ $location->text_short ?? 'Kein Beschreibungstext verfügbar' }}
                        </p>
                        @if(!empty($panorama_text_and_style))
                        <div style="background: {{ $panorama_text_and_style['style']['background'] }};
                                    color: {{ $panorama_text_and_style['style']['color'] }};
                                    font-family: {{ $panorama_text_and_style['style']['font'] }};
                                    padding: 20px;
                                    text-align: center;">
                            <p>{{ $panorama_text_and_style['text'] }}</p>
                        </div>
                    @endif
                    </div>
                </div>
            </div>

            <!-- Faktenkarte -->
            <div class="col-lg-5 col-md-12">
                <div class="card">
                    <div class="card-header text-center" style="background-color: #dbdbdb;">
                        <h4 class="text-uppercase">FAKTENCHECK</h4>
                    </div>
                    <div class="card-body bg-white pt-4 box-shadow-2">
                        <table class="table table-sm text-center">
                            <tr>
                                <td>
                                    <strong>Datum & Uhrzeit</strong>
                                    <div>{{ $current_time ?? 'Nicht verfügbar' }}</div>
                                </td>
                                <td>
                                    <strong>Hauptstadt</strong>
                                    <div>{{ $location->country->capital ?? 'Unbekannt' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Zeitverschiebung</strong>
                                    <div>{{ $time_offset ?? 'Nicht verfügbar' }} Stunden</div>
                                </td>
                                <td>
                                    <strong>Sprache</strong>
                                    <div>{{ $location->country->official_language ?? 'Nicht angegeben' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Preistendenz</strong>
                                    <div></div>
                                </td>
                                <td>
                                    <strong>Währung</strong>
                                    <div>{{ $location->country->currency_code ?? 'N/A' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Visum</strong>
                                    <div>{{ $location->country->country_visum_needed ? 'Nicht nötig' : $location->country->country_visum_max_time }}</div>
                                </td>
                                <td>
                                    <strong>Stromnetz</strong>
                                    <div>{{ $location->electric->power ?? 'N/A' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Flugzeit</strong>
                                    <div>{{ ceil($location->flight_hours ?? 0) }} Stunden</div>
                                </td>
                                <td>
                                    <strong>Entfernung</strong>
                                    <div>{{ number_format($location->dist_from_FRA, 0, ",", ".") }} km</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Preise -->
                <div class="card mb-3 box-shadow-2">
                    <div class="card-header text-center bg-light">
                        <h5 class="text-uppercase mb-0">Preise</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless text-center mb-0">
                            <tr>
                                <td>
                                    <i class="fa fa-hotel text-primary fa-1x mb-2"></i>
                                    <div class="fw-bold">Unterkünfte</div>
                                    <div>ab {{ number_format($location->price_hotel, 0, ",", ".") }} €</div>
                                </td>
                                <td>
                                    <i class="fa fa-plane text-primary fa-1x mb-2"></i>
                                    <div class="fw-bold">Flüge</div>
                                    <div>ab {{ number_format($location->price_flight, 0, ",", ".") }} €</div>
                                </td>
                                <td>
                                    <i class="fa fa-car text-primary fa-1x mb-2"></i>
                                    <div class="fw-bold">Mietwagen</div>
                                    <div>ab {{ number_format($location->price_rental, 0, ",", ".") }} €</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Aktivitäten -->
                <x-location-activities :locationId="$location->id" />
            </div>
        </div>
    </div>
</section>
