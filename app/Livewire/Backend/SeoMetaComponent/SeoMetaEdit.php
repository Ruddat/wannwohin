<?php

namespace App\Livewire\Backend\SeoMetaComponent;

use Livewire\Component;
use App\Models\ModSeoMeta;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SeoMetaEdit extends Component
{
    public $seoMeta;
    public $title;
    public $description;
    public $canonical;
    public $image;
    public $extraMeta = [];
    public $keywords = [];
    public $preventOverride = false;
    public $modelType;
    public $modelId;
    public $jsonError = null;

    public $extraMetaFields = [];
    public $keywordsFields = [];

    // Liste der häufig verwendeten Keywords-Schlüssel
    public $commonKeywordKeys = [
        'main' => 'Haupt-Keyword',
        'description' => 'Beschreibung',
        'tags' => 'Tags (kommasepariert)',
        'nextYear' => 'Nächstes Jahr (Zahl)',
    ];

    protected $rules = [
        'title'           => 'nullable|string|max:255',
        'description'     => 'nullable|string',
        'canonical'       => 'nullable|url',
        'image'           => 'nullable|url',
        'extraMeta'       => 'nullable|array',
        'keywords'        => 'nullable|array',
        'preventOverride' => 'boolean',
    ];

    public function mount($id)
    {
        $this->seoMeta = ModSeoMeta::findOrFail($id);
        $this->modelType = $this->seoMeta->model_type;
        $this->modelId   = $this->seoMeta->model_id;
        $this->title     = $this->seoMeta->title;
        $this->description = $this->seoMeta->description;
        $this->canonical   = $this->seoMeta->canonical;
        $this->image       = $this->seoMeta->image;

        $extraMeta = $this->seoMeta->extra_meta;
        if (!is_array($extraMeta)) {
            $extraMeta = json_decode($extraMeta, true) ?: [];
        }
        $this->extraMeta = $extraMeta;
        foreach ($this->extraMeta as $key => $value) {
            $this->extraMetaFields[] = ['key' => $key, 'value' => $value];
        }

        $keywords = $this->seoMeta->keywords;
        if (!is_array($keywords)) {
            $keywords = json_decode($keywords, true) ?: [];
        }
        $this->keywords = $keywords;
        $this->keywordsFields = [];
        foreach ($keywords as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $this->keywordsFields[] = ['key' => $key, 'value' => $value];
        }

        $this->preventOverride = $this->seoMeta->prevent_override;

        Log::info('Mounting SeoMetaEdit with extraMeta:', ['extraMeta' => $this->extraMeta]);
    }

    public function addExtraMetaField()
    {
        $this->extraMetaFields[] = ['key' => '', 'value' => ''];
    }

    public function removeExtraMetaField($index)
    {
        unset($this->extraMetaFields[$index]);
        $this->extraMetaFields = array_values($this->extraMetaFields);
    }

    public function addKeywordField()
    {
        $this->keywordsFields[] = ['key' => '', 'value' => ''];
    }

    public function removeKeywordField($index)
    {
        unset($this->keywordsFields[$index]);
        $this->keywordsFields = array_values($this->keywordsFields);
    }

    public function update()
    {
        $this->preventOverride = true;

        $extraMeta = [];
        foreach ($this->extraMetaFields as $field) {
            if (!empty($field['key'])) {
                $extraMeta[$field['key']] = $field['value'];
            }
        }
        $this->extraMeta = $extraMeta;

        $keywordsAssoc = [];
        foreach ($this->keywordsFields as $field) {
            if (!empty($field['key'])) {
                $key = $field['key'];
                $value = $field['value'];
                if ($key === 'tags') {
                    $value = array_map('trim', explode(',', $value));
                    $value = array_values($value);
                }
                if ($key === 'nextYear') {
                    $value = (int)$value;
                }
                $keywordsAssoc[$key] = $value;
            }
        }
        $this->keywords = $keywordsAssoc;

        $this->validate();

        $this->seoMeta->update([
            'title'           => $this->title,
            'description'     => $this->description,
            'canonical'       => $this->canonical,
            'image'           => $this->image,
            'extra_meta'      => json_encode($this->extraMeta),
            'keywords'        => json_encode($this->keywords),
            'prevent_override'=> $this->preventOverride,
        ]);

        // Cache löschen
        $cacheKey = "seo_{$this->modelType}_{$this->modelId}";
        Cache::forget($cacheKey);

        session()->flash('message', 'SEO-Eintrag erfolgreich aktualisiert.');
    }

    public function render()
    {
        return view('livewire.backend.seo-meta-component.seo-meta-edit', [
            'commonKeywordKeys' => $this->commonKeywordKeys,
        ])->layout('backend.layouts.livewiere-main');
    }
}
