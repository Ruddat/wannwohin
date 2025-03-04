<?php

namespace App\Livewire\Backend\SeoMetaComponent;

use Livewire\Component;
use App\Models\ModSeoMeta;
use Illuminate\Support\Facades\Log;

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
    public $jsonError = null; // Für JSON-Validierungsfehler

    protected $rules = [
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'canonical' => 'nullable|url',
        'image' => 'nullable|url',
        'extraMeta' => 'nullable|array',
        'keywords' => 'nullable|array',
        'preventOverride' => 'boolean',
    ];

    public function mount($id)
    {
        $this->seoMeta = ModSeoMeta::findOrFail($id);
        $this->modelType = $this->seoMeta->model_type;
        $this->modelId = $this->seoMeta->model_id;
        $this->title = $this->seoMeta->title;
        $this->description = $this->seoMeta->description;
        $this->canonical = $this->seoMeta->canonical;
        $this->image = $this->seoMeta->image;
        $this->extraMeta = $this->seoMeta->extra_meta ?? [];
        $this->keywords = $this->seoMeta->keywords ?? [];
        $this->preventOverride = $this->seoMeta->prevent_override;

        Log::info('Mounting SeoMetaEdit with extraMeta:', ['extraMeta' => $this->extraMeta]);
    }

    public function updateExtraMeta($jsonString)
    {
        try {
            $decoded = json_decode($jsonString, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Ungültiges JSON-Format: ' . json_last_error_msg());
            }
            $this->extraMeta = $decoded;
            $this->jsonError = null; // Fehler zurücksetzen, wenn JSON gültig ist
            Log::info('Updated extraMeta successfully:', ['extraMeta' => $this->extraMeta]);
        } catch (\Exception $e) {
            $this->jsonError = $e->getMessage();
            Log::error('JSON Error in updateExtraMeta:', ['error' => $e->getMessage()]);
        }
    }

    public function update()
    {
        if ($this->preventOverride) {
            $this->addError('preventOverride', 'Dieser Eintrag ist vor Überschreibungen geschützt.');
            return;
        }

        // Zusätzliche Validierung für extraMeta
        if (!is_array($this->extraMeta) && $this->extraMeta !== null) {
            $this->addError('extraMeta', 'Das Extra Meta muss ein gültiges JSON-Array oder null sein.');
            return;
        }

        $this->validate();

        $this->seoMeta->update([
            'title' => $this->title,
            'description' => $this->description,
            'canonical' => $this->canonical,
            'image' => $this->image,
            'extra_meta' => $this->extraMeta,
            'keywords' => $this->keywords,
            'prevent_override' => $this->preventOverride,
        ]);

        session()->flash('message', 'SEO-Eintrag erfolgreich aktualisiert.');
    }

    public function render()
    {
        return view('livewire.backend.seo-meta-component.seo-meta-edit')
            ->layout('backend.layouts.livewiere-main');
    }
}
