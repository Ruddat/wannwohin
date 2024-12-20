<div id="about-me">
    <section class="section section-no-border bg-color-light m-0">
        <div class="container">
            <div class="row">
                <div class="col">

                    <div class="custom-box-details bg-color-light custom-box-shadow-1 col-lg-6 ms-5 mb-5 mb-lg-4 float-end clearfix">
                        <h4>Top 10 Reiseziele</h4>
                        <div class="row  p-0 m-0">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <span class="text-color-dark">Pos.</span>
                                    </div>
                                    <div class="col-md-5">
                                        <span class="text-color-dark">Reiseziel</span>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="text-color-dark">Temp</span>
                                    </div>
                                    <div class="col-md-3 p-0 m-0">
                                        <span class="text-color-dark">Wetter</span>
                                    </div>
                                </div>
                                    @foreach($top_ten as $location)
                                    <div class="row p-0 m-0">
                                        <div class="col-md-2">
                                            <span class="text-color-dark">{{$loop->iteration}}.</span>
                                        </div>
                                        <div class="col-md-5">
                                            <img src="{{asset('img/flags_small/'.$location->country_alias.'.jpg')}}">
                                        </div>
                                        <div class="col-md-2">
                                            <span class="custom-text-color-2 text-nowrap">{{$location->current_temp_from_api}} °C</span>
                                        </div>
                                        <div class="col-md-3 p-0 m-0">
                                            <span class="custom-text-color-2 text-nowrap">{{$location->current_weather_from_api}}</span>
                                        </div>
                                    </div>
                                        @endforeach
                            </div>
{{--

                                    <li>
                                        <span class="custom-text-color-2">23°C</span>
                                        <span class="custom-text-color-2">Sonnenschein</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">7°C</span>
                                        <span class="custom-text-color-2">Bedeckt</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">28°C</span>
                                        <span class="custom-text-color-2">Regen</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">16°C</span>
                                        <span class="custom-text-color-2">Sonnenschein</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">15°C</span>
                                        <span class="custom-text-color-2">Bedeckt</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">7°C</span>
                                        <span class="custom-text-color-2">Sonnenschein</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">4°C</span>
                                        <span class="custom-text-color-2">Sturm</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">9°C</span>
                                        <span class="custom-text-color-2">Regen</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">25°C</span>
                                        <span class="custom-text-color-2">Sonnenschein</span>
                                    </li>
                                    <li>
                                        <span class="custom-text-color-2">-4°C</span>
                                        <span class="custom-text-color-2">Schneefall</span>
                                    </li>
                                </ul>--}}
                           {{-- </div>--}}
                        </div>
{{--                        <div class="row">--}}
{{--                            <div class="col-md-6">--}}
{{--                                <ul class="custom-list-style-1 p-0 mb-0">--}}
{{--                                    <li>--}}
{{--                                        <span class="text-color-dark">Birthday:</span>--}}
{{--                                        <span class="custom-text-color-2">1990 October 2</span>--}}
{{--                                    </li>--}}
{{--                                    <li>--}}
{{--                                        <span class="text-color-dark">Marital:</span>--}}
{{--                                        <span class="custom-text-color-2">Single</span>--}}
{{--                                    </li>--}}
{{--                                    <li>--}}
{{--                                        <span class="text-color-dark">Nationality:</span>--}}
{{--                                        <span class="custom-text-color-2">American</span>--}}
{{--                                    </li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-6">--}}
{{--                                <ul class="custom-list-style-1 p-0 mb-0">--}}
{{--                                    <li>--}}
{{--                                        <span class="text-color-dark">Skype:</span>--}}
{{--                                        <span class="custom-text-color-2"><a class="custom-text-color-2" href="skype:yourskype?chat">yourskype</a></span>--}}
{{--                                    </li>--}}
{{--                                    <li>--}}
{{--                                        <span class="text-color-dark">PHONE:</span>--}}
{{--                                        <span class="custom-text-color-2"><a class="custom-text-color-2" href="tel:123456789">123-456-789</a></span>--}}
{{--                                    </li>--}}
{{--                                    <li>--}}
{{--                                        <span class="text-color-dark">EMAIL:</span>--}}
{{--                                        <span class="custom-text-color-2"><a class="custom-text-color-2" href="mailto:me@domain.com">me@domain.com</a></span>--}}
{{--                                    </li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>

                    <h2 class="text-color-dark font-weight-extra-bold text-uppercase">ONLINE REISEFÜHRER</h2>

                    <p>Herzlich willkommen bei www.wann-wohin.de – Ihrem Online Reiseführer. Sie haben Urlaub und Lust auf einen Tapetenwechsel – wissen aber nicht wohin die nächste Reise gehen soll? Wir haben auf dieser Seite unterschiedliche Möglichkeiten für Sie zusammengestellt, mit denen Sie schnell und unkompliziert Ihr Traumziel finden.</p>
                    <p>Für Unentschlossene haben wir über 150 Urlaubsziele in verschiedene Kategorien eingeteilt. Diese finden Sie etwas weiter unten. Mit einem Klick geht’s auch schon los. Wenn Sie aber schon spezielle Vorstellungen von Ihrem Urlaub haben, können Sie direkt mit der Detailsuche von unserem Reise-Wizard starten. So finden auch Sie garantiert Ihr Traumziel – probieren Sie es einfach aus.</p>
                    <p>Das Team von wann-wohin wünscht Ihnen viel Spaß bei der Suche nach Ihrem nächsten Urlaubsziel.</p>


{{--
                    <div class="about-me-more" id="aboutMeMore">
                        <p>Für Unentschlossene haben wir über 150 Urlaubsziele in verschiedene Kategorien eingeteilt. Diese finden Sie etwas weiter unten. Mit einem Klick geht’s auch schon los. Wenn Sie aber schon spezielle Vorstellungen von Ihrem Urlaub haben, können Sie direkt mit der Detailsuche von unserem Reise-Wizard starten. So finden auch Sie garantiert Ihr Traumziel – probieren Sie es einfach aus.</p>
                        <p>Das Team von wann-wohin wünscht Ihnen viel Spaß bei der Suche nach Ihrem nächsten Urlaubsziel.</p>
                    </div>

                    <a id="aboutMeMoreBtn" class="btn btn-tertiary text-uppercase custom-btn-style-1 text-1" href="#">View More</a>
--}}

                </div>
            </div>
        </div>
    </section>
</div>
