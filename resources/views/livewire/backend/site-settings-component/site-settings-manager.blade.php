<div class="container mt-4">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit.prevent="save" class="card shadow-sm">
        <!-- Allgemeine Einstellungen -->
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Allgemeine Einstellungen</h3>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <!-- Website-Name -->
                <div class="col-md-6">
                    <label for="siteName" class="form-label">Website-Name</label>
                    <input type="text" id="siteName" class="form-control" wire:model="settings.site_name" placeholder="Dein Website-Name">
                </div>

                <!-- Website-URL -->
                <div class="col-md-6">
                    <label for="siteUrl" class="form-label">Website-URL</label>
                    <input type="url" id="siteUrl" class="form-control" wire:model="settings.site_url" placeholder="https://example.com">
                    @error('settings.site_url')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Kontakt-E-Mail -->
                <div class="col-md-6">
                    <label for="contactEmail" class="form-label">Kontakt-E-Mail</label>
                    <input type="email" id="contactEmail" class="form-control" wire:model="settings.contact_email" placeholder="kontakt@deinewebsite.de">
                </div>

                <!-- Inhaber -->
                <div class="col-md-6">
                    <label for="ownerName" class="form-label">Inhaber</label>
                    <input type="text" id="ownerName" class="form-control" wire:model="settings.owner_name" placeholder="Name des Inhabers">
                </div>

                <!-- Adresse: Straße & Hausnummer -->
                <div class="col-md-6">
                    <label for="addressStreet" class="form-label">Straße & Hausnummer</label>
                    <input type="text" id="addressStreet" class="form-control" wire:model="settings.address.street" placeholder="Musterstraße 123">
                </div>

                <!-- Adresse: PLZ & Ort -->
                <div class="col-md-6">
                    <label class="form-label">PLZ & Ort</label>
                    <div class="input-group">
                        <input type="text" class="form-control" wire:model="settings.address.zip" placeholder="PLZ">
                        <input type="text" class="form-control" wire:model="settings.address.city" placeholder="Ort">
                    </div>
                </div>

                <!-- Steuer-ID -->
                <div class="col-md-6">
                    <label for="taxId" class="form-label">Steuer-ID</label>
                    <input type="text" id="taxId" class="form-control" wire:model="settings.tax_id" placeholder="z. B. DE123456789">
                </div>

                <!-- Handelsregister -->
                <div class="col-md-6">
                    <label for="commercialRegister" class="form-label">Handelsregister</label>
                    <input type="text" id="commercialRegister" class="form-control" wire:model="settings.commercial_register" placeholder="HRB 12345">
                </div>

                <!-- Wartungsmodus -->
                <div class="col-12">
                    <label class="form-label">Wartungsmodus</label>
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
                                <label for="maintenanceStart" class="form-label">Startzeit</label>
                                <input type="datetime-local" id="maintenanceStart" class="form-control" wire:model="maintenance_start_at">
                                @error('maintenance_start_at')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="maintenanceEnd" class="form-label">Endzeit</label>
                                <input type="datetime-local" id="maintenanceEnd" class="form-control" wire:model="maintenance_end_at">
                                @error('maintenance_end_at')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Erlaubte IPs -->
                    <div class="col-12">
                        <label class="form-label">Erlaubte IPs</label>
                        @foreach ($maintenance_allowed_ips as $index => $ip)
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" wire:model="maintenance_allowed_ips.{{ $index }}" placeholder="z. B. 127.0.0.1">
                                <button type="button" class="btn btn-danger" wire:click="removeIp({{ $index }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            @error("maintenance_allowed_ips.{$index}")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endforeach
                        <button type="button" class="btn btn-outline-primary" wire:click="addIp">
                            <i class="ti ti-plus"></i> IP hinzufügen
                        </button>
                    </div>
                @endif

                <!-- Logo Upload -->
                <div class="col-md-6">
                    <label for="logoFile" class="form-label">Logo</label>
                    <input type="file" id="logoFile" class="form-control" wire:model="logoFile">
                    @if ($settings['logo'])
                        <div class="mt-2">
                            <img src="{{ $settings['logo'] }}" alt="Logo" class="img-thumbnail" style="max-width: 150px;">
                            <button type="button" class="btn btn-danger btn-sm mt-2" wire:click="deleteLogo">Löschen</button>
                        </div>
                    @endif
                </div>

                <!-- Favicon Preview -->
                <div class="col-md-6">
                    <label class="form-label">Favicon</label>
                    @if ($settings['favicon'])
                        <div class="mt-2">
                            <img src="{{ $settings['favicon'] }}" alt="Favicon" class="img-thumbnail" style="max-width: 32px;">
                        </div>
                    @endif
                </div>

                <!-- Apple Touch Icon Preview -->
                <div class="col-md-6">
                    <label class="form-label">Apple Touch Icon</label>
                    @if ($settings['apple_touch_icon'])
                        <div class="mt-2">
                            <img src="{{ $settings['apple_touch_icon'] }}" alt="Apple Icon" class="img-thumbnail" style="max-width: 180px;">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="card-header bg-secondary text-white">
            <h3 class="card-title mb-0">Social Media</h3>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-4">
                    <label for="facebookUrl" class="form-label">Facebook URL</label>
                    <input type="url" id="facebookUrl" class="form-control" wire:model="settings.facebook_url" placeholder="https://facebook.com/yourpage">
                </div>

                <div class="col-md-4">
                    <label for="twitterHandle" class="form-label">Twitter Handle</label>
                    <input type="text" id="twitterHandle" class="form-control" wire:model="settings.twitter_handle" placeholder="@deinprofil">
                </div>

                <div class="col-md-4">
                    <label for="instagramUrl" class="form-label">Instagram URL</label>
                    <input type="url" id="instagramUrl" class="form-control" wire:model="settings.instagram_url" placeholder="https://instagram.com/yourprofile">
                </div>
            </div>
        </div>

        <!-- SEO-Einstellungen -->
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">SEO-Einstellungen</h3>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6">
                    <label for="googleAnalyticsId" class="form-label">Google Analytics ID</label>
                    <input type="text" id="googleAnalyticsId" class="form-control" wire:model="settings.google_analytics_id" placeholder="UA-XXXXXX-X">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Standard-SEO-Keywords</label>
                    <div>
                        @foreach ($settings['default_meta_keywords'] as $index => $keyword)
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" wire:model="settings.default_meta_keywords.{{ $index }}" placeholder="Keyword">
                                <button type="button" class="btn btn-danger" wire:click="removeKeyword({{ $index }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        @endforeach
                        <button type="button" class="btn btn-outline-primary" wire:click="addKeyword">
                            <i class="ti ti-plus"></i> Keyword hinzufügen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-success">
                <i class="ti ti-check"></i> Speichern
            </button>
        </div>
    </form>
</div>
