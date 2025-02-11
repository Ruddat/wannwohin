<section id="experience" class="section section-secondary section-no-border m-0">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="text-color-dark text-uppercase font-weight-extra-bold mb-0">@autotranslate('WELCHER URLAUBSTYP SIND SIE?', app()->getLocale())</h2>
                <h5 class="text-color-dark">@autotranslate('und in welchem Monat wollen Sie verreisen?', app()->getLocale())</h5>
                <div class="row col-lg-3 ms-4">
                    <select class="form-select urlaub_type_month" id="urlaub_type_month" onchange="updateSearchResults()">
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ $month == 6 ? 'selected' : '' }}>
                                @autotranslate(\Carbon\Carbon::create()->month($month)->translatedFormat('F'), app()->getLocale())
                            </option>
                        @endforeach
                    </select>
                </div>

                <section class="timeline custom-timeline" id="timeline">
                    <div class="timeline-body">

                        <!-- Reisearten -->
                        @php
                            $travelTypes = [
                                [
                                    'url' => url("/urlaub/strand-reise"),
                                    'image' => 'img/startpage/meer-wasser-und-strand.webp',
                                    'title' => 'Meer, Wasser und Strand',
                                    'description' => 'Du liebst die Weite des Meeres, den feinen Strandsand und möchtest am Liebsten im Rauschen der Wellen Deine Seele baumeln lassen? Hier findest Du die besten Urlaubsziele in Wassernähe.'
                                ],
                                [
                                    'url' => url("/urlaub/natur-reise"),
                                    'image' => 'img/startpage/urlaub-in-der-natur.webp',
                                    'title' => 'Natürlich Urlaub - aber nur in der Natur!',
                                    'description' => 'Dem Alltags-Stress entkommst du am Besten im Grünen. Morgens schon mit Vogelgezwitscher aufwachen oder einfach die Weite der Berge geniessen? Beeindruckende Natur-Reiseziele sind hier zu finden.'
                                ],
                                [
                                    'url' => url("/urlaub/staedte-reise"),
                                    'image' => 'img/startpage/kein-stillstand-hauptsache-in-der-stadt.webp',
                                    'title' => 'Kein Stillstand - Hauptsache Stadt',
                                    'description' => 'Trubel, Action und das pulsierende Lebensgefühl einer Stadt lässt Dich höher und weiter treiben? Hier sind die schönsten Städtereisen zum Shoppen, Genießen oder einfach Spaß haben zusammengefasst.'
                                ],
                                [
                                    'url' => url("/urlaub/kultur-reise"),
                                    'image' => 'img/startpage/culture-beat-kultur-und-geschichte.webp',
                                    'title' => 'Culture Beat - Kultur und Geschichte',
                                    'description' => 'Du interessierst Dich für monumentale Bauwerke, geschichtsträchtige Orte und Menschen, die in der Vergangenheit Großes geleistet haben? Hier findest Du die kulturell interessantesten Reiseziele.'
                                ],
                                [
                                    'url' => url("/urlaub/insel-reise"),
                                    'image' => 'img/startpage/insel-urlaub.webp',
                                    'title' => 'Was würdest Du mit auf eine einsame Insel nehmen?',
                                    'description' => 'Deinen Urlaub verbringst Du am liebsten weit weg von allem - Hauptsache auf einer kleinen Insel? Zugegeben: Es gibt auch richtig große Inseln - aber beide haben eins gemeinsam: Du findest Sie hier.'
                                ],
                                [
                                    'url' => url("/urlaub/wintersport-reise"),
                                    'image' => 'img/startpage/winter-urlaub.webp',
                                    'title' => 'Ich stehe auf coolen Urlaub',
                                    'description' => 'Du freust Dich auf Schneeflocken und liebst das Glitzern der Sonne auf dem Schnee? Die klare Luft und die Sportmöglichkeiten in der weißen Pracht bringen Dich auf andere Gedanken? Hier geht\'s lang für einen Winterurlaub.'
                                ],
                                [
                                    'url' => url("/urlaub/sport-reise"),
                                    'image' => 'img/startpage/aktiv-urlaub.webp',
                                    'title' => 'Zeit für mich - mit Sport vor Ort',
                                    'description' => 'Sport ist für Dich eine Passion und du schaltest am Besten ab, wenn Du alleine oder mit Freunden tauchst, Mountainbike fährst oder Wintersport machst? Hier geht\'s zum Fitnessprogramm und den besten Locations dafür.'
                                ]
                            ];
                        @endphp

                        @foreach($travelTypes as $type)
                        <a href="{{ $type['url'] }}" class="p-0 m-0 urlaub-type-url">
                            <article class="timeline-box right custom-box-shadow-2">
                                <div class="my-zoom1">
                                    <div class="row">
                                        <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
                                            <img class="w-100 img-fill" src="{{ asset($type['image']) }}">
                                        </div>
                                        <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                            <h4 class="text-color-dark font-weight-semibold">@autotranslate($type['title'], app()->getLocale())</h4>
                                            <p class="custom-text-color-2">@autotranslate($type['description'], app()->getLocale())</p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </a>
                        <div class="timeline-bar"></div>
                        @endforeach

                    </div>

                    <div class="timeline-body">
                        @php
                            $travelTypes = \App\Models\ModQuickFilterItem::where('status', 1)
                                ->orderBy('sort_order', 'asc')
                                ->get();
                        @endphp

                        @foreach($travelTypes as $type)
                        <a href="{{ url('/urlaub/' . $type->slug) }}" class="p-0 m-0 urlaub-type-url">
                            <article class="timeline-box right custom-box-shadow-2">
                                <div class="my-zoom1">
                                    <div class="row">
                                        <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
                                            <img class="w-100 img-fill" src="{{ asset('storage/' . $type->thumbnail) }}">
                                        </div>
                                        <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                            <h4 class="text-color-dark font-weight-semibold">@autotranslate($type->title, app()->getLocale()) - @autotranslate($type->title_text, app()->getLocale())</h4>
                                            <p class="custom-text-color-2">@autotranslate($type->content, app()->getLocale())</p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </a>
                        <div class="timeline-bar"></div>
                        @endforeach
                    </div>




                </section>
            </div>
        </div>
    </div>
</section>



<style>

/* Grundstruktur */
.timeline-bar {
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #007bff; /* Hauptfarbe der Linie */
    z-index: -1; /* Hinter die Artikel legen */
    transform: translateX(-50%);
}

.timeline-box.right .timeline-bar {
    left: 50%; /* Linker Startpunkt für die Linie */
}

section.timeline .timeline-box.right {
    clear: right;
    float: right;
    right: 1px;
    margin-top: 40px;
}

.timeline-box:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease-in-out;
}

.timeline-box img {
    transition: transform 0.3s ease-in-out;
}

.timeline-box:hover img {
    transform: scale(1.1);
}


.timeline-box:nth-child(even) .experience-description {
    text-align: left;
}

.timeline-box:nth-child(odd) .experience-description {
    text-align: right;
}

.timeline-box .experience-info img {
    border-radius: 8px;
}


/* Inhalt */
.experience-info img {
    max-width: 100%;
    object-fit: cover;
}

.experience-description {
    padding: 20px;
}

/* Desktop-Layout */
.col-lg-4 {
    flex: 0 0 33.33%;
    max-width: 33.33%;
}

.col-lg-8 {
    flex: 0 0 66.66%;
    max-width: 66.66%;
}

/* Tablets */
@media (max-width: 992px) {
    .col-lg-4,
    .col-lg-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .timeline-box {
        flex-direction: column;
    }

    .experience-info img {
        max-height: 250px;
    }

    .experience-description {
        padding: 15px;
        text-align: center;
    }
}

/* Smartphones */
@media (max-width: 768px) {
    .experience-description {
        text-align: center;
        padding: 10px;
    }

    .experience-info img {
        max-height: 200px;
    }

    h4 {
        font-size: 1.25rem;
    }

    p {
        font-size: 0.9rem;
    }
}

</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const timelineBars = document.querySelectorAll('.timeline-bar');
    timelineBars.forEach((bar, index) => {
        const parent = bar.closest('.timeline-box');
        const nextSibling = parent.nextElementSibling;
        if (nextSibling) {
            const height = nextSibling.offsetTop - parent.offsetTop;
            bar.style.height = `${height}px`;
        }
    });

    window.addEventListener('resize', function () {
        timelineBars.forEach((bar) => {
            const parent = bar.closest('.timeline-box');
            const nextSibling = parent.nextElementSibling;
            if (nextSibling) {
                const height = nextSibling.offsetTop - parent.offsetTop;
                bar.style.height = `${height}px`;
            }
        });
    });
});
</script>
