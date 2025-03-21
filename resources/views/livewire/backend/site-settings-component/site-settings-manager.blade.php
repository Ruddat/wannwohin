
    <div class="container-fluid">
        <!-- Breadcrumb start -->
        <div class="row m-1">
            <div class="col-12">
                <h5>Allgemeine Einstellungen</h5>
                <ul class="app-line-breadcrumbs mb-3">
                    <li>
                        <a href="#" class="f-s-14 f-w-500">
                            <span><i class="ph-duotone ph-gear f-s-16"></i> Einstellungen</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="#" class="f-s-14 f-w-500">Allgemeine Einstellungen</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb end -->

        <!-- Message -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Form start -->
        <div class="row table-section">
            <div class="col-xl-12">
                <form wire:submit.prevent="save">
                    <div class="card">
                        <!-- Allgemeine Einstellungen -->
                        <div class="card-header text-bg-primary">
                            <h5>Allgemeine Einstellungen</h5>
                            <p class="mb-0">Konfiguriere die grundlegenden Website-Einstellungen</p>
                        </div>
                        <div class="card-body">
                            <div class="row gy-3">
                                <!-- Website-Name -->
                                <div class="col-md-6">
                                    <label for="siteName" class="form-label f-w-500">Website-Name</label>
                                    <input type="text" id="siteName" class="form-control" wire:model="settings.site_name" placeholder="Dein Website-Name">
                                </div>

                                <!-- Website-URL -->
                                <div class="col-md-6">
                                    <label for="siteUrl" class="form-label f-w-500">Website-URL</label>
                                    <input type="url" id="siteUrl" class="form-control" wire:model="settings.site_url" placeholder="https://example.com">
                                    @error('settings.site_url')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Kontakt-E-Mail -->
                                <div class="col-md-6">
                                    <label for="contactEmail" class="form-label f-w-500">Kontakt-E-Mail</label>
                                    <input type="email" id="contactEmail" class="form-control" wire:model="settings.contact_email" placeholder="kontakt@deinewebsite.de">
                                </div>

                                <!-- Inhaber -->
                                <div class="col-md-6">
                                    <label for="ownerName" class="form-label f-w-500">Inhaber</label>
                                    <input type="text" id="ownerName" class="form-control" wire:model="settings.owner_name" placeholder="Name des Inhabers">
                                </div>

                                <!-- Adresse: Straße & Hausnummer -->
                                <div class="col-md-6">
                                    <label for="addressStreet" class="form-label f-w-500">Straße & Hausnummer</label>
                                    <input type="text" id="addressStreet" class="form-control" wire:model="settings.address.street" placeholder="Musterstraße 123">
                                </div>

                                <!-- Adresse: PLZ & Ort -->
                                <div class="col-md-6">
                                    <label class="form-label f-w-500">PLZ & Ort</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" wire:model="settings.address.zip" placeholder="PLZ">
                                        <input type="text" class="form-control" wire:model="settings.address.city" placeholder="Ort">
                                    </div>
                                </div>

                                <!-- Steuer-ID -->
                                <div class="col-md-6">
                                    <label for="taxId" class="form-label f-w-500">Steuer-ID</label>
                                    <input type="text" id="taxId" class="form-control" wire:model="settings.tax_id" placeholder="z. B. DE123456789">
                                </div>

                                <!-- Handelsregister -->
                                <div class="col-md-6">
                                    <label for="commercialRegister" class="form-label f-w-500">Handelsregister</label>
                                    <input type="text" id="commercialRegister" class="form-control" wire:model="settings.commercial_register" placeholder="HRB 12345">
                                </div>

                                <!-- Wartungsmodus -->
                                <div class="col-12">
                                    <label class="form-label f-w-500">Wartungsmodus</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="maintenanceMode" wire:model.change="maintenance_mode">
                                        <label class="form-check-label" for="maintenanceMode">Aktivieren</label>
                                    </div>
                                </div>

                                @if ($maintenance_mode)
                                    <!-- Start- und Endzeit -->
                                    <div class="col-12">
                                        <div class="row gy-3">
                                            <div class="col-md-6">
                                                <label for="maintenanceStart" class="form-label f-w-500">Startzeit</label>
                                                <input type="datetime-local" id="maintenanceStart" class="form-control" wire:model="maintenance_start_at">
                                                @error('maintenance_start_at')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="maintenanceEnd" class="form-label f-w-500">Endzeit</label>
                                                <input type="datetime-local" id="maintenanceEnd" class="form-control" wire:model="maintenance_end_at">
                                                @error('maintenance_end_at')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Erlaubte IPs -->
                                    <div class="col-12">
                                        <label class="form-label f-w-500">Erlaubte IPs</label>
                                        @foreach ($maintenance_allowed_ips as $index => $ip)
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" wire:model="maintenance_allowed_ips.{{ $index }}" placeholder="z. B. 127.0.0.1">
                                                <button type="button" class="btn text-bg-danger border-0" wire:click="removeIp({{ $index }})">
                                                    <i class="ph-duotone ph-trash"></i>
                                                </button>
                                            </div>
                                            @error("maintenance_allowed_ips.{$index}")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        @endforeach
                                        <button type="button" class="btn text-bg-primary border-0" wire:click="addIp">
                                            <i class="ph-duotone ph-plus"></i> IP hinzufügen
                                        </button>
                                    </div>
                                @endif

                                <!-- Logo Upload -->
                                <div class="col-md-6">
                                    <label for="logoFile" class="form-label f-w-500">Logo</label>
                                    <input type="file" id="logoFile" class="form-control" wire:model="logoFile">
                                    @if ($settings['logo'])
                                        <div class="mt-2">
                                            <img src="{{ $settings['logo'] }}" alt="Logo" class="img-fluid b-r-10" style="max-width: 150px;">
                                            <button type="button" class="btn text-bg-danger border-0 btn-sm mt-2" wire:click="deleteLogo">Löschen</button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Favicon Preview -->
                                <div class="col-md-6">
                                    <label class="form-label f-w-500">Favicon</label>
                                    @if ($settings['favicon'])
                                        <div class="mt-2">
                                            <img src="{{ $settings['favicon'] }}" alt="Favicon" class="img-fluid b-r-10" style="max-width: 32px;">
                                        </div>
                                    @endif
                                </div>

                                <!-- Apple Touch Icon Preview -->
                                <div class="col-md-6">
                                    <label class="form-label f-w-500">Apple Touch Icon</label>
                                    @if ($settings['apple_touch_icon'])
                                        <div class="mt-2">
                                            <img src="{{ $settings['apple_touch_icon'] }}" alt="Apple Icon" class="img-fluid b-r-10" style="max-width: 180px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="card-header text-bg-primary">
                            <h5>Social Media</h5>
                            <p class="mb-0">Verknüpfe deine Social-Media-Profile</p>
                        </div>
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-md-4">
                                    <label for="facebookUrl" class="form-label f-w-500">Facebook URL</label>
                                    <input type="url" id="facebookUrl" class="form-control" wire:model="settings.facebook_url" placeholder="https://facebook.com/yourpage">
                                </div>
                                <div class="col-md-4">
                                    <label for="twitterHandle" class="form-label f-w-500">Twitter Handle</label>
                                    <input type="text" id="twitterHandle" class="form-control" wire:model="settings.twitter_handle" placeholder="@deinprofil">
                                </div>
                                <div class="col-md-4">
                                    <label for="instagramUrl" class="form-label f-w-500">Instagram URL</label>
                                    <input type="url" id="instagramUrl" class="form-control" wire:model="settings.instagram_url" placeholder="https://instagram.com/yourprofile">
                                </div>
                            </div>
                        </div>

                        <!-- SEO-Einstellungen -->
                        <div class="card-header text-bg-primary">
                            <h5>SEO-Einstellungen</h5>
                            <p class="mb-0">Optimiere deine Website für Suchmaschinen</p>
                        </div>
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label for="googleAnalyticsId" class="form-label f-w-500">Google Analytics ID</label>
                                    <input type="text" id="googleAnalyticsId" class="form-control" wire:model="settings.google_analytics_id" placeholder="UA-XXXXXX-X">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label f-w-500">Standard-SEO-Keywords</label>
                                    @foreach ($settings['default_meta_keywords'] as $index => $keyword)
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" wire:model="settings.default_meta_keywords.{{ $index }}" placeholder="Keyword">
                                            <button type="button" class="btn text-bg-danger border-0" wire:click="removeKeyword({{ $index }})">
                                                <i class="ph-duotone ph-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn text-bg-primary border-0" wire:click="addKeyword">
                                        <i class="ph-duotone ph-plus"></i> Keyword hinzufügen
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="card-footer text-end">
                            <button type="submit" class="btn text-bg-success border-0">
                                <i class="ph-duotone ph-check"></i> Speichern
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Form end -->
    </div>

