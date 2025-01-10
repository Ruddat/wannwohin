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
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        {{ config('app.available_locales')[App::getLocale()]['name'] }}
                    </a>
                    <ul class="dropdown-menu">
                        @foreach (config('app.available_locales') as $localeCode => $locale)
                            <li>
                                <a href="{{ route('change.lang', ['lang' => $localeCode]) }}">
                                    {{ $locale['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Newsletter Section -->
            <div class="col-md-6 col-lg-3 mb-3">
                <h5>Newsletter</h5>
                <form>
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Deine Email" />
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Abonnieren</button>
                </form>
            </div>
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
