<section id="experience" class="section section-secondary section-no-border m-0">
    <div class="container">
        <!-- Beispiel: Header -->
        <div class="header">
            <x-ad-block slot="header" />
        </div>

        <div class="row">
            <div class="col">
                <h2 class="text-color-dark text-uppercase font-weight-extra-bold mb-0">@autotranslate('WELCHER URLAUBSTYP SIND SIE?', app()->getLocale())</h2>
                <h5 class="text-color-dark">@autotranslate('und in welchem Monat wollen Sie verreisen?', app()->getLocale())</h5>
                <div class="row col-lg-3 ms-4">
                    <select class="form-select urlaub_type_month" id="urlaub_type_month" name="month">
                        <option value="">Monat auswählen</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ $month == 6 ? 'selected' : '' }}>
                                @autotranslate(\Carbon\Carbon::create()->month($month)->translatedFormat('F'), app()->getLocale())
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Timeline mit AOS -->
                <ul class="timeline">
                    @php
                        $travelTypes = \App\Models\ModQuickFilterItem::where('status', 1)
                            ->orderBy('sort_order', 'asc')
                            ->get();
                        $urlaubTypeMap = [
                            'strand-reise' => 'list_beach',
                            'staedte-reise' => 'list_citytravel',
                            'sport-reise' => 'list_sports',
                            'insel-reise' => 'list_island',
                            'kultur-reise' => 'list_culture',
                            'natur-reise' => 'list_nature',
                            'wassersport-reise' => 'list_watersport',
                            'wintersport-reise' => 'list_wintersport',
                            'mountainsport-reise' => 'list_mountainsport',
                            'biking-reise' => 'list_biking',
                            'fishing-reise' => 'list_fishing',
                            'amusement-park-reise' => 'list_amusement_park',
                            'water-park-reise' => 'list_water_park',
                            'animal-park-reise' => 'list_animal_park',
                        ];
                    @endphp

                    @forelse($travelTypes as $index => $type)
                        <li data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <div class="timeline-content">
                                <div class="card-container">
                                    <!-- Image Section -->
                                    <div class="card-image zoom-effect"
                                         style="background-image: url('{{ asset('storage/' . $type->thumbnail) }}')">
                                        <a href="#" onclick="redirectToSearch('{{ $type->slug }}'); return false;"
                                           class="image-link"></a>
                                    </div>

                                    <!-- Content Section -->
                                    <a href="#" onclick="redirectToSearch('{{ $type->slug }}'); return false;"
                                       class="card-link">
                                        <div class="card-details">
                                            <div class="card-header">
                                                <h4 class="location-title-2">
                                                    @autotranslate($type->title, app()->getLocale()) - @autotranslate($type->title_text, app()->getLocale())
                                                </h4>
                                            </div>
                                            <div class="card-info">
                                                <p class="custom-text-color-2">
                                                    @autotranslate($type->content, app()->getLocale())
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                    @empty
                        <p class="text-center">@autotranslate('Keine Urlaubstypen gefunden.', app()->getLocale())</p>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</section>

@vite(['resources/backend/css/app.css', 'resources/backend/js/app.js'])

<script>
    const urlaubTypeMap = @json($urlaubTypeMap);

    function redirectToSearch(urlaubType) {
        const monthId = document.getElementById('urlaub_type_month').value;
        if (!monthId) {
            alert('Bitte wählen Sie einen Monat aus.');
            return;
        }
        const mappedType = urlaubTypeMap[urlaubType] || urlaubType;
        const url = "{{ route('search.results') }}?urlaub=" + monthId + "&spezielle=" + mappedType;
        window.location.href = url;
    }
</script>

<style>
    /* Timeline-Grundstruktur */
    ul.timeline {
        list-style-type: none;
        position: relative;
        padding: 0;
        margin: 0;
        z-index: 0; /* Basis-z-index für die Timeline */
    }

    ul.timeline:before {
        content: '';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 30px;
        width: 2px;
        height: 100%;
        z-index: 1; /* Linie auf niedrigem Level */
    }

    ul.timeline > li {
        margin: 50px 0;
        padding-left: 60px;
        position: relative;
        z-index: 2; /* Li über der Linie */
    }

    ul.timeline > li:before {
        content: '';
        background: white;
        display: inline-block;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 21px;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        width: 20px;
        height: 20px;
        z-index: 3; /* Punkt über allem */
        transition: background 0.3s ease;
    }

    ul.timeline > li:hover:before {
        background: #22c0e8;
    }

    .timeline-content {
        background: #fff;
        padding: 0;
        border-radius: 10px;
        box-shadow: none;
        transition: transform 0.2s ease-in-out;
        text-align: left;
        position: relative;
        z-index: 2; /* Inhalt über der Linie */
    }

    ul.timeline > li:hover .timeline-content {
        transform: translateY(-5px);
    }

    /* Angepasster Card-Container */
    .card-container {
        display: flex;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
        min-height: 200px;
        position: relative;
        z-index: 2; /* Card über der Linie */
    }

    /* Einheitliche Bildbreite und Höhe */
    .card-image {
        flex: 0 0 200px; /* Feste Breite mit flex */
        height: 200px; /* Feste Höhe */
        background-size: cover;
        background-position: center;
        position: relative;
        transition: transform 0.5s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    /* Hover-Effekt für das Bild */
    .card-image:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    /* Schimmer-Effekt */
    .card-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: -75%;
        width: 50%;
        height: 100%;
        background: linear-gradient(to right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.1));
        transform: skewX(-25deg);
        transition: left 0.7s ease-in-out;
    }

    .card-image:hover::before {
        left: 125%;
    }

    .card-details {
        flex: 1; /* Nimmt den restlichen Platz ein */
        padding: 20px;
    }

    .card-header {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .location-title-2 {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
    }

    .card-info p {
        margin: 0;
        color: #666;
    }

    .image-link {
        display: block;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
    }

    .card-link {
        display: block;
        text-decoration: none;
        color: inherit;
    }

    /* Responsive Anpassungen */
    @media (max-width: 768px) {
        .card-container {
            flex-direction: column;
            min-height: unset;
        }

        .card-image {
            flex: 0 0 auto; /* Zurück auf auto für volle Breite */
            width: 100%;
            height: 150px;
        }

        .card-image:hover {
            transform: none;
            box-shadow: none;
        }

        .card-details {
            width: 100%;
        }

        ul.timeline:before {
            left: 15px;
        }

        ul.timeline > li {
            padding-left: 40px;
        }

        ul.timeline > li:before {
            left: 6px;
        }
    }

    @media (max-width: 576px) {
        ul.timeline:before, ul.timeline > li:before {
            display: none;
        }

        ul.timeline > li {
            padding-left: 0;
            text-align: center;
        }

        .timeline-content {
            margin: 0 auto;
            max-width: 90%;
        }
    }
</style>

<style>
    .ad-block {
        margin: 20px 0;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .ad-block img {
        max-width: 100%;
        height: auto;
    }
</style>
