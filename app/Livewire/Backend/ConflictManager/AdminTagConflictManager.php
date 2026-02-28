<?php

namespace App\Livewire\Backend\ConflictManager;

use App\Models\CategoryMapping;
use App\Models\WwdeTag;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AdminTagConflictManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selectedTag = [];

    public function saveMapping($rawCategory)
    {
        if (!isset($this->selectedTag[$rawCategory])) {
            return;
        }

        $tagId = $this->selectedTag[$rawCategory];

        // Mapping speichern oder aktualisieren
        CategoryMapping::updateOrCreate(
            ['raw_category' => $rawCategory],
            ['tag_id' => $tagId]
        );

        // 🔥 Konflikte auflösen
        $conflicts = DB::table('wwde_tag_conflicts')
            ->where('raw_category', $rawCategory)
            ->get();

        foreach ($conflicts as $conflict) {

            // Tag an Location anhängen (Pivot Tabelle anpassen falls nötig)
            DB::table('wwde_location_tag')->updateOrInsert([
                'location_id' => $conflict->location_id,
                'tag_id' => $tagId,
            ]);

        }

        // Konflikte löschen
        DB::table('wwde_tag_conflicts')
            ->where('raw_category', $rawCategory)
            ->delete();

        unset($this->selectedTag[$rawCategory]);

        $this->resetPage();
    }

    public function render()
    {
        $conflicts = DB::table('wwde_tag_conflicts')
            ->select('raw_category', DB::raw('COUNT(*) as count'))
            ->groupBy('raw_category')
            ->orderByDesc('count')
            ->paginate(20);

        $tags = WwdeTag::orderBy('group')
            ->orderBy('title')
            ->get();

        return view('livewire.backend.conflict-manager.admin-tag-conflict-manager', [
            'conflicts' => $conflicts,
            'tags' => $tags,
        ])->layout('raadmin.layout.master');
    }
}
