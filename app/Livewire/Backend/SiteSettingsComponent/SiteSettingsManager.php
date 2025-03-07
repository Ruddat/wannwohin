<?php

namespace App\Livewire\Backend\SiteSettingsComponent;

use Imagick;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ModSiteSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Drivers\Gd\Encoders\PngEncoder;

class SiteSettingsManager extends Component
{
    use WithFileUploads;

    public $settings = [];
    public $logoFile;
    public $maintenance_mode = false;
    public $maintenance_start_at; // Neues Feld für Startzeit
    public $maintenance_end_at;   // Neues Feld für Endzeit
    public $maintenance_allowed_ips = []; // Neues Feld für erlaubte IPs

    protected $rules = [
        'settings.site_name' => 'required|string|max:255',
        'settings.site_url' => 'required|url|max:255',
        'settings.contact_email' => 'required|email|max:255',
        'settings.owner_name' => 'nullable|string|max:255',
        'settings.address.street' => 'nullable|string|max:255',
        'settings.address.zip' => 'nullable|string|max:10',
        'settings.address.city' => 'nullable|string|max:255',
        'settings.tax_id' => 'nullable|string|max:50',
        'settings.commercial_register' => 'nullable|string|max:100',
        'settings.facebook_url' => 'nullable|url|max:255',
        'settings.twitter_handle' => 'nullable|string|max:50',
        'settings.instagram_url' => 'nullable|url|max:255',
        'settings.google_analytics_id' => 'nullable|string|max:20',
        'settings.default_meta_keywords' => 'nullable|array',
        'settings.default_meta_keywords.*' => 'string|max:50',
        'logoFile' => 'nullable|image|max:2048',
        'maintenance_mode' => 'boolean',
        'maintenance_start_at' => 'nullable|date', // Validierung für Datum/Zeit
        'maintenance_end_at' => 'nullable|date|after:maintenance_start_at', // Muss nach Startzeit sein
        'maintenance_allowed_ips' => 'nullable|array',
        'maintenance_allowed_ips.*' => 'ip', // Jeder Eintrag muss eine gültige IP sein
    ];

    public function mount()
    {

        // Cache für relevante Schlüssel leeren, um sicherzustellen, dass wir aktuelle Daten laden
        Cache::forget('site_setting_default_meta_keywords');
        Cache::forget('site_setting_maintenance_mode');
        Cache::forget('site_setting_maintenance_start_at');
        Cache::forget('site_setting_maintenance_end_at');
        Cache::forget('site_setting_maintenance_allowed_ips');

        $this->settings = ModSiteSettings::getPublicSettings();
        $this->settings['default_meta_keywords'] = ModSiteSettings::get('default_meta_keywords', ['reisen', 'urlaub']);        $this->maintenance_mode = ModSiteSettings::get('maintenance_mode', false);
        $this->maintenance_start_at = ModSiteSettings::get('maintenance_start_at');
        $this->maintenance_end_at = ModSiteSettings::get('maintenance_end_at');
        $this->maintenance_allowed_ips = ModSiteSettings::get('maintenance_allowed_ips', ['127.0.0.1']);

        $this->settings = array_merge([
            'site_name' => 'WannWohin.de',
            'site_url' => 'https://wannwohin.de',
            'logo' => '/storage/uploads/logo.png',
            'favicon' => '/storage/uploads/icons/favicon.ico',
            'apple_touch_icon' => '/storage/uploads/icons/apple-touch-icon.png',
            'owner_name' => '',
            'contact_email' => '',
            'facebook_url' => '',
            'twitter_handle' => '',
            'instagram_url' => '',
            'google_analytics_id' => '',
        ], $this->settings);
    }

    public function save()
    {
        $this->validate();

        $maintenanceService = app(\App\Services\MaintenanceService::class);

        foreach ($this->settings as $key => $value) {
            $type = match ($key) {
                'default_meta_keywords' => 'json',
                'logo', 'favicon', 'apple_touch_icon' => 'file',
                default => 'string',
            };

            ModSiteSettings::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value, 'type' => $type, 'group' => $this->getGroupForKey($key)]
            );
        }

        // Wartungsmodus über den Service verwalten
        if ($this->maintenance_mode) {
            $maintenanceService->enableMaintenanceMode(
                $this->settings['maintenance_message'] ?? 'Wartungsmodus aktiv', // Fallback-Nachricht
                $this->maintenance_start_at,
                $this->maintenance_end_at,
                $this->maintenance_allowed_ips
            );
        } else {
            $maintenanceService->disableMaintenanceMode();
        }

        if ($this->logoFile) {
            try {
                $path = $this->logoFile->store('uploads', 'public');
                $this->generateIcons($path);
                ModSiteSettings::updateOrCreate(
                    ['key' => 'logo'],
                    ['value' => Storage::url($path), 'type' => 'file', 'group' => 'general']
                );
                $this->settings['logo'] = Storage::url($path);
            } catch (\Exception $e) {
                $this->addError('logoFile', 'Fehler beim Hochladen des Logos: ' . $e->getMessage());
                return;
            }
        }

        session()->flash('message', 'Einstellungen erfolgreich gespeichert!');
    }

    public function deleteLogo()
    {
        if ($this->settings['logo'] && Storage::disk('public')->exists(str_replace('/storage/', '', $this->settings['logo']))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $this->settings['logo']));
        }
        ModSiteSettings::where('key', 'logo')->update(['value' => null]);
        $this->settings['logo'] = null;
        session()->flash('message', 'Logo wurde gelöscht!');
    }

    public function addKeyword()
    {
        $this->settings['default_meta_keywords'][] = '';

        // Sofort in der Datenbank speichern
        ModSiteSettings::updateOrCreate(
            ['key' => 'default_meta_keywords'],
            ['value' => json_encode($this->settings['default_meta_keywords']), 'type' => 'json', 'group' => 'seo']
        );
    }

    public function removeKeyword($index)
    {

        unset($this->settings['default_meta_keywords'][$index]);
        $this->settings['default_meta_keywords'] = array_values($this->settings['default_meta_keywords']);

        // Sofort in der Datenbank speichern
        ModSiteSettings::updateOrCreate(
            ['key' => 'default_meta_keywords'],
            ['value' => json_encode($this->settings['default_meta_keywords']), 'type' => 'json', 'group' => 'seo']
        );
    }

    public function addIp()
    {
        $this->maintenance_allowed_ips[] = '';
    }

    public function removeIp($index)
    {
        unset($this->maintenance_allowed_ips[$index]);
        $this->maintenance_allowed_ips = array_values($this->maintenance_allowed_ips);
    }

    private function getGroupForKey($key)
    {
        return match (true) {
            str_starts_with($key, 'facebook') || str_starts_with($key, 'twitter') || str_starts_with($key, 'instagram') => 'social',
            str_contains($key, 'meta') || str_contains($key, 'google_analytics') => 'seo',
            default => 'general',
        };
    }

    private function generateIcons($imagePath)
    {
        $image = Image::read(Storage::disk('public')->path($imagePath));
        $iconPath = 'uploads/icons/';
        Storage::disk('public')->makeDirectory($iconPath);

        $faviconPngPath = $iconPath . 'favicon.png';
        $faviconIcoPath = $iconPath . 'favicon.ico';
        $faviconImage = $image->resize(32, 32, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode(new PngEncoder(), 100);
        Storage::disk('public')->put($faviconPngPath, (string) $faviconImage);

        if (extension_loaded('imagick')) {
            $imagick = new Imagick();
            $imagick->readImageBlob(Storage::disk('public')->get($faviconPngPath));
            $imagick->setImageFormat('ico');
            Storage::disk('public')->put($faviconIcoPath, $imagick->getImageBlob());
            $imagick->clear();
            $imagick->destroy();
        } else {
            $faviconIcoPath = $faviconPngPath;
        }

        ModSiteSettings::updateOrCreate(['key' => 'favicon'], ['value' => Storage::url($faviconIcoPath), 'type' => 'file', 'group' => 'general']);
        $this->settings['favicon'] = Storage::url($faviconIcoPath);

        $appleTouchIconPath = $iconPath . 'apple-touch-icon.png';
        $appleTouchIcon = $image->resize(180, 180, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode(new PngEncoder(), 100);
        Storage::disk('public')->put($appleTouchIconPath, (string) $appleTouchIcon);

        ModSiteSettings::updateOrCreate(['key' => 'apple_touch_icon'], ['value' => Storage::url($appleTouchIconPath), 'type' => 'file', 'group' => 'general']);
        $this->settings['apple_touch_icon'] = Storage::url($appleTouchIconPath);
    }

    public function render()
    {
        return view('livewire.backend.site-settings-component.site-settings-manager')
        ->layout('backend.layouts.livewiere-main');
    }
}
