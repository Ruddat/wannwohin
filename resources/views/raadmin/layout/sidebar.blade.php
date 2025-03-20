<!-- Menu Navigation starts -->
<nav>
    <div class="app-logo">
        <a class="logo d-inline-block" href="#">
            <img src="{{ asset('assets\ra-admin\images\logo\1-neu.png') }}" alt="#">
        </a>

        <span class="bg-light-primary toggle-semi-nav">
          <i class="ti ti-chevrons-right f-s-20"></i>
        </span>
    </div>
    <div class="app-nav" id="app-simple-bar">
        <ul class="main-nav p-0 mt-2">
            <li class="menu-title">
                <span>Dashboard</span>
            </li>
            <li>
                <a class="" data-bs-toggle="collapse" href="#dashboard" aria-expanded="false">
                    <i class="ph-duotone  ph-house-line"></i>
                    dashboard
                    <span class="badge text-bg-success badge-notification ms-2">1</span>
                </a>
                <ul class="collapse" id="dashboard">
                    <li><a href="{{ route('verwaltung.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('verwaltung.seo-table-manager.visitor-stats') }}">Analytics</a></li>
                </ul>
            </li>


            <li>
                <a class="" data-bs-toggle="collapse" href="#SiteManager" aria-expanded="false">
                    <i class="ph-duotone  ph-stack"></i>
                    Site Manager
                </a>
                <ul class="collapse" id="SiteManager">
                    <li><a href="{{ route('verwaltung.site-manager.header_contents.index') }}">Header Text & Pic</a></li>
                    <li><a href="{{ route('verwaltung.site-manager.park-manager.index') }}">Freizeitparks</a></li>
                    <li><a href="{{ route('verwaltung.site-manager.quick-filter') }}">QuickFilter Home</a></li>
                    <li><a href="{{ route('verwaltung.site-manager.continent-manager.index') }}">Continents</a></li>
                    <li><a href="{{ route('verwaltung.site-manager.country-manager.index') }}">Countrys</a></li>
                    <li><a href="{{ route('verwaltung.site-manager.location-table-manager.index') }}">City Manager</a></li>


                    <li class="another-level">
                        <a class="" data-bs-toggle="collapse" href="#More-page" aria-expanded="false">
                            More
                        </a>
                        <ul class="collapse" id="More-page">
                            <li><a href="{{ route('verwaltung.electric-manager.index') }}">Electric Manager</a></li>
                            <li><a href="{{ route('verwaltung.gallery-manager.index') }}">Gallery Manager</a></li>
                        </ul>
                    </li>
                </ul>
            </li>



            <li class="menu-title"> <span>Import & Scrape</span></li>
            <li>
                <a class="" href="#Imports" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="ph-duotone  ph ph-download"></i> Imports
                </a>
                <ul class="collapse" id="Imports">
                    <li><a href="{{ route('verwaltung.addons.imports-pics.view') }}">Import Pictures</a></li>
                    <li><a href="{{ route('verwaltung.weather-importer.index') }}">Stations Importer</a></li>
                    <li><a href="{{ route('verwaltung.admin.commands.view') }}">Artisan Command</a></li>
                </ul>
            </li>


            <li class="menu-title"> <span>System</span></li>
            <li>
                <a class="" href="#settingMore" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="ph-duotone  ph ph-gear"></i> Settings & more
                </a>
                <ul class="collapse" id="settingMore">
                    <li><a href="{{ route('verwaltung.seo-table-manager.backup.index') }}">Backups</a></li>
                    <li><a href="{{ route('verwaltung.seo-table-manager.site-settings') }}">Site Settings</a></li>
                    <li><a href="{{ route('verwaltung.seo-table-manager.static-page-manager') }}">Statische Seiten</a></li>
                    <li><a href="{{ route('verwaltung.translation-manager.index') }}">Translation Manager</a></li>
                </ul>
            </li>


            <li>
                <a class="" href="#seoManager" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="ph-duotone  ph ph-magnifying-glass"></i> Seo Manager
                </a>
                <ul class="collapse" id="seoManager">
                    <li><a href="{{ route('verwaltung.seo-table-manager.seo.table') }}">Seo Dynamic</a></li>
                </ul>
            </li>


            <li>
                <a class="" href="#wetterManager" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="ph-duotone  ph ph-thermometer"></i> Weather
                </a>
                <ul class="collapse" id="wetterManager">
                    <li><a href="{{ route('verwaltung.weather-climate-manager.index') }}">Clima Data Manager</a></li>
                    <li><a href="{{ route('verwaltung.weather-manager.index') }}">Weather Stations</a></li>
                </ul>
            </li>


            <li>
                <a class="" href="#werbungManager" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="ph-duotone  ph ph-megaphone"></i> Werbung
                </a>
                <ul class="collapse" id="werbungManager">
                    <li><a href="{{ route('verwaltung.advertisement-manager.advertisement-providers') }}">Werbeprovider</a></li>
                    <li><a href="{{ route('verwaltung.advertisement-manager.advertisement-blocks') }}">Werbeblöcke</a></li>
                </ul>
            </li>








            <li class="menu-title"> <span>Others</span></li>













            <li class="no-sub">

            </li>


        </ul>
    </div>

    <div class="menu-navs">
        <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
        <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
    </div>

</nav>
<!-- Menu Navigation ends -->
