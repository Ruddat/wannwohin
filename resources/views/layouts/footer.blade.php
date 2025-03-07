<footer class="footer bg-dark text-white py-5">

    <div class="container">
        <div class="row gy-4">
            <!-- Navigation Links -->
            <div class="col-12 col-md-3 text-center text-md-start">
                <h5 class="mb-3">Navigation</h5>
                <ul class="list-unstyled">
                    <li><a href="/impressum" class="footer-link">Impressum</a></li>
                    <li><a href="/datenschutz" class="footer-link">Datenschutz</a></li>
                    <li><a href="/kontakt" class="footer-link">Kontakt</a></li>
                    <li><a href="/verwaltung/login" class="footer-link">Verwaltung</a></li>
                </ul>
            </div>

            <!-- Social Links -->
            <div class="col-12 col-md-3 text-center">
                <h5 class="mb-3">Folge uns</h5>
                <div class="d-flex justify-content-center gap-3">
                    <a href="https://facebook.com" class="social-link" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://twitter.com" class="social-link" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://instagram.com" class="social-link" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://linkedin.com" class="social-link" target="_blank">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>

            <!-- Language Switcher -->
            <div class="col-12 col-md-3 text-center">
                <h5 class="mb-3">Sprache</h5>
                <div class="dropdown d-inline-block">
                    <a href="#" class="dropdown-toggle d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" style="text-decoration: none; color: #fff;">
                        @php
                            $currentLocale = config('app.available_locales')[App::getLocale()];
                            $currentFlag = asset('assets/fonts/flag-icons-master/4x3/' . strtolower(substr($currentLocale['flag'], -3)) . '.svg');
                        @endphp
                        <img src="{{ $currentFlag }}" alt="{{ $currentLocale['name'] }}" class="me-2" style="width: 24px; height: 16px; border-radius: 3px;">
                        {{ $currentLocale['name'] }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        @foreach (config('app.available_locales') as $localeCode => $locale)
                            @php
                                $flag = asset('assets/fonts/flag-icons-master/4x3/' . strtolower(substr($locale['flag'], -3)) . '.svg');
                            @endphp
                            <li>
                                <a href="{{ route('change.lang', ['lang' => $localeCode]) }}" class="dropdown-item d-flex align-items-center">
                                    <img src="{{ $flag }}" alt="{{ $locale['name'] }}" class="me-2" style="width: 24px; height: 16px; border-radius: 3px;">
                                    {{ $locale['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Newsletter Section -->
            <div class="col-12 col-md-3 text-center text-md-start">
                <h5 class="mb-3">Newsletter</h5>
                @livewire('frontend.newsletter-form.newsletter-form-component')
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom text-center mt-5 pt-4 border-top border-secondary">
            <p class="small mb-0">© 2025 Dein Unternehmen. Alle Rechte vorbehalten.</p>
        </div>
    </div>
</footer>
