<section id="experience" class="section section-secondary section-no-border m-0">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="text-color-dark text-uppercase font-weight-extra-bold mb-0">WELCHER URLAUBSTYP SIND SIE?</h2>
                <h5 class="text-color-dark">und in welchem Monat wollen Sie verreisen?</h5>
                <div class="row col-lg-3 ms-4">
                    <select class="form-select urlaub_type_month" id="urlaub_type_month" onchange="updateSearchResults()">
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ $month == 6 ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <section class="timeline custom-timeline" id="timeline">
                    <div class="timeline-body">
                        <a href="{{url("/urlaub/strand-reise")}}" class="p-0 m-0 urlaub-type-url">
                        <article class="timeline-box right custom-box-shadow-2">
                            <div class="my-zoom1">
                            <div class="row">
                                <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
{{--                                    <a href="{{route('urlaub_type','meer-wasser-und-strand')}}" class="p-0 m-0">--}}
                                            <img class="w-100 img-fill" src="{{asset('img/startpage/meer-wasser-und-strand.webp')}}">
{{--                                    </a>--}}
                                </div>
                                <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                    <h4 class="text-color-dark font-weight-semibold">Meer, Wasser und Strand</h4>
                                    <p class="custom-text-color-2">Du liebst die Weite des Meeres, den feinen Strandsand und möchtest am Liebsten im Rauschen der Wellen Deine Seele baumeln lassen? Hier findest Du die besten Urlaubsziele in Wassernähe.</p>
                                </div>
                            </div>
                            </div>
                        </article>
                        </a>
                        <a href="{{url("/urlaub/natur-reise") }}" class="p-0 m-0 urlaub-type-url">
                        <article class="timeline-box right custom-box-shadow-2">
                            <div class="my-zoom1">
                            <div class="row">
                                <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
{{--                                    <a href="#" class="p-0 m-0">--}}
{{--                                        <div class="my-zoom">--}}
                                            <img class="w-100 img-fill" src="{{asset('img/startpage/urlaub-in-der-natur.webp')}}">
{{--                                        </div>--}}
{{--                                    </a>--}}
                                </div>
                                <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                    <h4 class="text-color-dark font-weight-semibold">Natürlich Urlaub - aber nur in der Natur!</h4>
                                    <p class="custom-text-color-2">Dem Alltags-Stress entkommst du am Besten im Grünen. Morgens schon mit Vogelgezwitscher aufwachen oder einfach die Weite der Berge geniessen? Beeindruckende Natur-Reiseziele sind hier zu finden.</p>
                                </div>
                            </div>
                            </div>
                        </article>
                        </a>
                        <a href="{{ url("/urlaub/staedte-reise")}}" class="p-0 m-0 urlaub-type-url">
                        <article class="timeline-box right custom-box-shadow-2">
                            <div class="my-zoom1">
                            <div class="row">
                                <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
{{--                                    <a href="#" class="p-0 m-0">--}}
{{--                                        <div class="my-zoom">--}}
                                            <img class="w-100 img-fill" src="{{asset('img/startpage/kein-stillstand-hauptsache-in-der-stadt.webp')}}">
{{--                                        </div>--}}
{{--                                    </a>--}}
                                </div>

                                <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                        <h4 class="text-color-dark font-weight-semibold">Kein Stillstand - Hauptsache Stadt</h4>
                                        <p class="custom-text-color-2">Trubel, Action und das pulsierende Lebensgefühl einer Stadt lässt Dich höher und weiter treiben? Hier sind die schönsten Städtereisen zum Shoppen, Genießen oder einfach Spaß haben zusammengefasst.</p>
                                </div>
                            </div>
                            </div>
                        </article>
                        </a>
                        <a href="{{ url("/urlaub/kultur-reise")}}" class="p-0 m-0 urlaub-type-url">
                        <article class="timeline-box right custom-box-shadow-2">
                            <div class="my-zoom1">
                            <div class="row">
                                <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
{{--                                    <a href="#" class="p-0 m-0">--}}
                                            <img class="w-100 img-fill" src="{{asset('img/startpage/culture-beat-kultur-und-geschichte.webp')}}">
{{--                                    </a>--}}
                                </div>
                                <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                    <h4 class="text-color-dark font-weight-semibold">Culture Beat - Kultur und Geschichte</h4>
                                    <p class="custom-text-color-2">Du interessierst Dich für monumentale Bauwerke, geschichtsträchtige Orte und Menschen, die in der Vergangenheit Großes geleistet haben? Hier findest Du die kulturell interessantesten Reiseziele.</p>
                                </div>
                            </div>
                            </div>
                        </article>
                        </a>
                        <a href="{{ url("/urlaub/insel-reise")}}" class="p-0 m-0 urlaub-type-url">
                        <article class="timeline-box right custom-box-shadow-2">
                            <div class="my-zoom1">
                            <div class="row">
                                <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
{{--                                    <a href="#" class="p-0 m-0">--}}
                                            <img class="w-100 img-fill" src="{{asset('img/startpage/insel-urlaub.webp')}}">
{{--                                    </a>--}}
                                </div>
                                <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                    <h4 class="text-color-dark font-weight-semibold">Was würdest Du mit auf eine einsame Insel nehmen?</h4>
                                    <p class="custom-text-color-2">Deinen Urlaub verbringst Du am liebsten weit weg von allem - Hauptsache auf einer kleinen Insel? Zugegeben: Es gibt auch richtig große Inseln - aber beide haben eins gemeinsam: Du findest Sie hier.</p>
                                </div>
                            </div>
                            </div>
                        </article>
                        </a>
                        <a href="{{url("/urlaub/wintersport-reise") }}" class="p-0 m-0 urlaub-type-url">
                        <article class="timeline-box right custom-box-shadow-2">
                            <div class="my-zoom1">
                            <div class="row">
                                <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
{{--                                    <a href="#" class="p-0 m-0">--}}
                                            <img class="w-100 img-fill" src="{{asset('img/startpage/winter-urlaub.webp')}}">

{{--                                    </a>--}}
                                </div>
                                <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                    <h4 class="text-color-dark font-weight-semibold">Ich stehe auf coolen Urlaub</h4>
                                    <p class="custom-text-color-2">Du freust Dich auf Schneeflocken und liebst das Glitzern der Sonne auf dem Schnee? Die klare Luft und die Sportmöglichkeiten in der weißen Pracht bringen Dich auf andere Gedanken? Hier geht's lang für einen Winterurlaub.</p>
                                </div>
                            </div>
                            </div>
                        </article>
                        </a>
                        <a href="{{ url("/urlaub/sport-reise") }}" class="p-0 m-0 urlaub-type-url">
                        <article class="timeline-box right custom-box-shadow-2">
                            <div class="my-zoom1">
                            <div class="row">
                                <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
{{--                                    <a href="#" class="p-0 m-0">--}}
                                            <img class="w-100 img-fill" src="{{asset('img/startpage/aktiv-urlaub.webp')}}">

{{--                                    </a>--}}
                                </div>
                                <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                    <h4 class="text-color-dark font-weight-semibold">Zeit für mich - mit Sport vor Ort</h4>
                                    <p class="custom-text-color-2">Sport ist für Dich eine Passion und du schaltest am Besten ab, wenn Du alleine oder mit Freunden tauchst, Mountainbike fährst oder Wintersport machst? Hier geht's zum Fitnessprogramm und den besten Locations dafür.</p>
                                </div>
                            </div>
                            </div>
                        </article>
                        </a>
                        <div class="timeline-bar"></div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>









<style>

/* Grundstruktur */
.timeline-box {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

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
