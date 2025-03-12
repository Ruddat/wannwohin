<?php

namespace App\Livewire\Frontend\LocationInspiration;

use GuzzleHttp\Client;
use Livewire\Component;
use App\Models\ModLocationFilter;

class LocationInspirationComponent extends Component
{
    public $locationId;
    public $locationTitle;
    public $suggestions = [];

    public $selected = null;        // Aktuell ausgewählte Inspiration (bei Unterkategorie)
    public $selectedType = null;    // Gewählter Haupttyp (Sport, Erlebnis, Freizeitpark)
    public $categories = [];        // Unterkategorien für den ausgewählten Haupttyp

    public $randomSuggestions = []; // Drei zufällige Vorschläge
    public $randomMode = false;     // Wenn true, sind wir im Random‑Mode

    public $tripPlan = [];

    public $deepInfraApiToken;

    public function mount($locationId, $locationTitle)
    {
        $this->locationId = $locationId;
        $this->locationTitle = $locationTitle;

        $this->suggestions = ModLocationFilter::where('location_id', $locationId)
            ->where('is_active', 1)
            ->get()
            ->groupBy('text_type')
            ->toArray();

        $this->tripPlan = session('trip_plan', []);
        $this->deepInfraApiToken = config('services.deepinfra.api_token');

     //   dd($this->hfApiToken);
    }

    /**
     * Beim Klick auf eine der Haupt-Kacheln: zeige die Unterkategorien.
     */
    public function showCategories($type)
    {
        $this->randomMode = false; // falls vorher Random aktiv war
        $this->selectedType = $type;
        $this->categories = ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1)
            ->where('text_type', $type)
            ->pluck('category')
            ->unique()
            ->values()
            ->toArray();
        $this->selected = null;
    }

    /**
     * Zeigt eine zufällige Inspiration aus der gewählten Unterkategorie an.
     */
    public function showCategorySuggestion($category)
    {
        $items = ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1)
            ->where('category', $category)
            ->get()
            ->toArray();

        if (count($items) > 0) {
            $this->selected = $items[array_rand($items)];
            // Hier könnte man auch ein Event dispatchen, falls Animationen via Alpine.js gewünscht sind
        }
    }

    /**
     * Wechselt in den Random‑Mode: zeigt drei zufällige Vorschläge und blendet die Hauptkacheln aus.
     */
    public function randomInspiration()
    {
        $this->randomMode = true;
        $this->selectedType = null;
        $this->categories = [];

        if (!$this->deepInfraApiToken) {
            $this->randomSuggestions = $this->generateFallbackSuggestions('Kein DeepInfra API-Token konfiguriert.');
            $this->randomInspirationFromDatabase();
            return;
        }

        $cacheKey = 'random_suggestions_' . md5($this->locationTitle);
        $cachedSuggestions = \Cache::get($cacheKey);

        if ($cachedSuggestions) {
            $this->randomSuggestions = $cachedSuggestions;
            return;
        }

        $uschrifts = ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1)
            ->pluck('uschrift')
            ->filter()
            ->unique()
            ->implode(', ');

        $client = new Client();
        try {
            $response = $client->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->deepInfraApiToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input' => "[INST] Gib mir genau drei kreative und einzigartige Aktivitäten für einen Trip nach {$this->locationTitle}. Nummeriere sie als 1., 2., 3. und füge für jede Aktivität einen spezifischen Ort oder eine Sehenswürdigkeit in {$this->locationTitle} hinzu, markiert als [Ort: Name]. Nutze diese bekannten Orte als Inspiration: {$uschrifts}. Halte jeden Vorschlag kurz (max. 30 Wörter). [/INST]",
                    'max_new_tokens' => 150,
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ],
                'timeout' => 10,
            ]);

            $data = json_decode($response->getBody(), true);
            $suggestionsText = $data['results'][0]['generated_text'] ?? 'Keine Antwort generiert.';
            \Log::info('Raw Suggestions:', [$suggestionsText]);
            $this->randomSuggestions = $this->parseSuggestions($suggestionsText);

            \Cache::put($cacheKey, $this->randomSuggestions, now()->addHours(24));
        } catch (\Exception $e) {
            \Log::error('DeepInfra API Error:', ['message' => $e->getMessage()]);
            $this->randomInspirationFromDatabase();
        }
    }

    /**
     * Fallback-Methode: Hole zufällige Vorschläge aus der Datenbank (alter Code)
     */
    private function randomInspirationFromDatabase()
    {
        $all = [];
        foreach ($this->suggestions as $group) {
            $all = array_merge($all, $group);
        }

        if (count($all) === 0) {
            $this->randomSuggestions = $this->generateFallbackSuggestions('Keine Vorschläge in der Datenbank verfügbar.');
            return;
        }

        // Gewichte basierend auf Tripplan (optional)
        $weights = collect($this->tripPlan)->groupBy('text_type')->map->count()->toArray();
        $weightedSuggestions = collect($all)->map(function ($item) use ($weights) {
            $item['weight'] = $weights[$item['text_type']] ?? 0;
            return $item;
        })->sortByDesc('weight')->values();

        $this->randomSuggestions = $weightedSuggestions->take(3)->toArray();
        \Log::info('Fallback to Database:', ['randomSuggestions' => $this->randomSuggestions]);
    }

    private function parseSuggestions($text)
    {
        $lines = array_filter(explode("\n", trim($text)), fn($line) => !empty($line));
        $suggestions = [];

        foreach ($lines as $line) {
            if (preg_match('/^\d+\.\s*(.+?)\s*\[Ort:\s*(.+?)\]/', $line, $matches)) {
                $suggestions[] = [
                    'text_type' => 'Random',
                    'category' => 'Ki Vorschlag',
                    'text' => trim($matches[1]),
                    'uschrift' => trim($matches[2]),
                    'id' => uniqid('di_', true),
                ];
            } elseif (preg_match('/^\d+\.\s*(.+)$/', $line, $matches)) {
                // Fallback für nicht markierte Antworten
                $suggestionText = trim($matches[1]);
                $uschrift = $this->extractUschrift($suggestionText);
                $suggestions[] = [
                    'text_type' => 'Random',
                    'category' => 'Ki Vorschlag',
                    'text' => $suggestionText,
                    'uschrift' => $uschrift ?: 'Aktivität',
                    'id' => uniqid('di_', true),
                ];
            }
        }

        // Falls weniger als 3 Einträge, Absätze als Fallback
        if (count($suggestions) < 3 && count($lines) > 0) {
            $suggestions = [];
            $counter = 1;
            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    if (preg_match('/^\d+\.\s*(.+?)\s*\[Ort:\s*(.+?)\]/', $line, $matches)) {
                        $suggestions[] = [
                            'text_type' => 'Random',
                            'category' => 'Ki Vorschlag',
                            'text' => trim($matches[1]),
                            'uschrift' => trim($matches[2]),
                            'id' => uniqid('di_', true),
                        ];
                    } else {
                        $suggestionText = trim($line);
                        $uschrift = $this->extractUschrift($suggestionText);
                        $suggestions[] = [
                            'text_type' => 'Random',
                            'category' => 'Ki Vorschlag',
                            'text' => $suggestionText,
                            'uschrift' => $uschrift ?: 'Aktivität',
                            'id' => uniqid('di_', true),
                        ];
                    }
                    $counter++;
                    if ($counter > 3) break;
                }
            }
        }

        if (count($suggestions) < 3) {
            $suggestions = array_merge($suggestions, $this->generateFallbackSuggestions());
        }

        return array_slice($suggestions, 0, 3);
    }

    /**
     * Fallback-Extraktion für nicht markierte Antworten
     */
    private function extractUschrift($text)
    {
        $patterns = [
            '/an\s+der\s+(.+?)(?:\s|$)/i',  // "an der East Side Gallery"
            '/entlang\s+des\s+(.+?)(?:\s|$)/i', // "entlang des Spreeparks"
            '/in\s+(.+?)(?:\s|$)/i',         // "in Frankfurt"
            '/am\s+(.+?)(?:\s|$)/i',         // "am Römerberg"
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }

        // Fallback: Nimm das erste substantivische Element nach dem Verb
        $words = explode(' ', $text);
        $verbs = ['Entdecke', 'Miete', 'Fahre', 'Besuche'];
        $verbFound = false;
        foreach ($words as $word) {
            if ($verbFound && !empty($word) && !in_array($word, ['ein', 'eine', 'und'])) {
                return trim($word);
            }
            if (in_array($word, $verbs)) {
                $verbFound = true;
            }
        }

        return '';
    }

    private function generateFallbackSuggestions($message = null)
    {
        $suggestions = [
            ['id' => 'fb1', 'text_type' => 'Random', 'category' => 'Fallback', 'text' => "Entdecke {$this->locationTitle} zu Fuß."],
            ['id' => 'fb2', 'text_type' => 'Random', 'category' => 'Fallback', 'text' => "Probiere lokale Spezialitäten in {$this->locationTitle}."],
            ['id' => 'fb3', 'text_type' => 'Random', 'category' => 'Fallback', 'text' => "Besuche ein verstecktes Juwel in {$this->locationTitle}."],
        ];
        if ($message) {
            $suggestions[] = ['id' => 'fb_error', 'text_type' => 'Random', 'category' => 'Info', 'text' => $message];
        }
        return array_slice($suggestions, 0, 3);
    }

    /**
     * Setzt den Zustand zurück: Hauptkacheln und Unterkategorien werden wieder angezeigt.
     */
    public function resetSelection()
    {
        $this->selectedType = null;
        $this->categories = [];
        $this->selected = null;
        $this->randomSuggestions = [];
        $this->randomMode = false;
    }

    public function removeFromTripPlan($suggestionId)
    {
        $this->tripPlan = collect($this->tripPlan)
            ->reject(function($item) use ($suggestionId) {
                return $item['id'] == $suggestionId;
            })
            ->values()
            ->toArray();
        session()->put('trip_plan', $this->tripPlan);
    }

    /**
     * Fügt einen Vorschlag dem Tripplan hinzu und speichert diesen in der Session.
     */
    public function addToTripPlan($id)
    {
        // Suche in Random-Vorschlägen
        $item = collect($this->randomSuggestions)->firstWhere('id', $id);

        // Falls nicht gefunden und ein Haupttyp ausgewählt ist, suche in Unterkategorien
        if (!$item && $this->selectedType && isset($this->suggestions[$this->selectedType])) {
            $item = collect($this->suggestions[$this->selectedType])->firstWhere('id', $id);
        }

        // Falls immer noch nicht gefunden, prüfe die Datenbank (für Kompatibilität mit altem Code)
        if (!$item) {
            $suggestion = ModLocationFilter::find($id);
            if ($suggestion) {
                $item = $suggestion->toArray();
                $item['estimated_time'] = rand(1, 3) . ' Stunden'; // Platzhalter aus altem Code
            }
        }

        if ($item) {
            if (!collect($this->tripPlan)->contains('id', $id)) {
                $this->tripPlan[] = $item;
                session(['trip_plan' => $this->tripPlan]);
                \Log::info('Added to Trip Plan:', ['id' => $id, 'item' => $item]);
            } else {
                \Log::info('Item already in Trip Plan:', ['id' => $id]);
            }
        } else {
            \Log::error('Item not found for Trip Plan:', ['id' => $id, 'randomSuggestions' => $this->randomSuggestions]);
        }
    }
    public function getTripPlanData()
    {
        return $this->tripPlan;
    }

    public function clearTripPlan()
    {
        $this->tripPlan = [];
        session()->put('trip_plan', $this->tripPlan);
        \Log::info('Tripplan gelöscht');
    }


    public function render()
    {
        return view('livewire.frontend.location-inspiration.location-inspiration-component');
    }
}
