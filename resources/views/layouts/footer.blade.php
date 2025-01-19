<footer class="footer bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <!-- Navigation Links -->
            <div class="col-md-6 col-lg-3 mb-3">
                <h5>Navigation</h5>
                <ul class="list-unstyled">
                    <li><a href="/impressum" class="footer-link">Impressum</a></li>
                    <li><a href="/datenschutz" class="footer-link">Datenschutz</a></li>
                    <li><a href="/kontakt" class="footer-link">Kontakt</a></li>
                    <li><a href="/verwaltung/login" class="footer-link">Verwaltung</a></li>
                </ul>
            </div>

            <!-- Social Links -->
            <div class="col-md-6 col-lg-3 mb-3">
                <h5>Folge uns</h5>
                <div class="social-links">
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
<div class="col-md-6 col-lg-3 mb-3">
    <h5>Sprache</h5>
    <div class="dropdown">
        <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" style="text-decoration: none; color: #fff;">
            <!-- Aktuelle Sprache mit Flagge -->
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
            <div class="col-md-6 col-lg-3 mb-3">
                @livewire('frontend.newsletter-form.newsletter-form-component')
            </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom text-center mt-4">
            <p class="small mb-0">© 2025 Dein Unternehmen. Alle Rechte vorbehalten.</p>
        </div>
    </div>
</footer>


<style>
/* Footer Styling */
.footer {
    background-color: #222;
    color: #fff;
}

.footer h5 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.footer a {
    color: #fff;
    text-decoration: none;
}

.footer a:hover {
    color: #fdd55c;
    text-decoration: underline;
}

/* Dropdown Styling */
.footer .dropdown-menu {
    background-color: #333; /* Dunkler Hintergrund für den Dropdown */
    border: 1px solid #444;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}

.footer .dropdown-item {
    color: #fff;
    transition: background-color 0.2s, color 0.2s;
}

.footer .dropdown-item:hover {
    background-color: #444;
    color: #fdd55c;
}

.footer .dropdown-item img {
    margin-right: 8px;
    border-radius: 3px;
}

/* Social Links */
.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    color: #fff;
    font-size: 1.5rem;
    transition: transform 0.3s ease, color 0.3s ease;
}

.social-link:hover {
    transform: scale(1.2);
    color: #fdd55c;
}

/* Footer Bottom */
.footer-bottom {
    border-top: 1px solid #444;
    padding-top: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer h5 {
        text-align: center;
    }

    .footer .social-links {
        justify-content: center;
    }

    .footer .row > div {
        text-align: center;
    }
}


</style>
