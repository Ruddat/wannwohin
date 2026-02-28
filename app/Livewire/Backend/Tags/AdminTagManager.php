<?php

namespace App\Livewire\Backend\Tags;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class AdminTagManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $q = '';
    public ?string $group = null;
    public string $sort = 'usage'; // usage|title|group
    public string $dir = 'desc';
    public int $perPage = 25;

    /** @var array<int,int|string|null> sourceTagId => targetTagId */
    public array $mergeTarget = [];

    /** @var array<int,string> tagId => newTitle */
    public array $renameTitle = [];

    /** @var array<int,string> tagId => newGroup */
    public array $renameGroup = [];

    public bool $showSuggestions = true;

    public function updatingQ(): void { $this->resetPage(); }
    public function updatingGroup(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }
    public function updatingDir(): void { $this->resetPage(); }

    public function rename(int $tagId): void
    {
        $title = trim($this->renameTitle[$tagId] ?? '');
        $group = trim($this->renameGroup[$tagId] ?? '');

        if ($title === '' && $group === '') {
            return;
        }

        $tag = DB::table('wwde_tags')->where('id', $tagId)->first();
        if (!$tag) return;

        $newTitle = $title !== '' ? $title : $tag->title;
        $newGroup = $group !== '' ? $group : $tag->group;

        DB::table('wwde_tags')->where('id', $tagId)->update([
            'title' => $newTitle,
            'group' => $newGroup,
            'slug'  => Str::slug($newTitle),
            'updated_at' => now(),
        ]);

        unset($this->renameTitle[$tagId], $this->renameGroup[$tagId]);
    }

    public function merge(int $sourceId): void
    {
        $targetId = (int)($this->mergeTarget[$sourceId] ?? 0);
        if ($targetId <= 0 || $targetId === $sourceId) return;

        DB::transaction(function () use ($sourceId, $targetId) {

            // how many relations will be moved?
            $moved = (int) DB::table('wwde_location_tag')->where('tag_id', $sourceId)->count();

            // 1) move relations
            DB::table('wwde_location_tag')
                ->where('tag_id', $sourceId)
                ->update(['tag_id' => $targetId]);

            // 2) remove duplicates (if unique constraint exists later)
            DB::table('wwde_location_tag')
                ->select('location_id')
                ->where('tag_id', $targetId)
                ->groupBy('location_id')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('location_id')
                ->each(function ($locationId) use ($targetId) {
                    // Keep one row, delete the rest
                    $ids = DB::table('wwde_location_tag')
                        ->where('tag_id', $targetId)
                        ->where('location_id', $locationId)
                        ->orderBy('id')
                        ->pluck('id')
                        ->toArray();

                    array_shift($ids);
                    if (!empty($ids)) {
                        DB::table('wwde_location_tag')->whereIn('id', $ids)->delete();
                    }
                });

            // 3) log (optional)
            if (DB::getSchemaBuilder()->hasTable('wwde_tag_merges')) {
                DB::table('wwde_tag_merges')->insert([
                    'source_tag_id' => $sourceId,
                    'target_tag_id' => $targetId,
                    'moved_relations' => $moved,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 4) delete source tag
            DB::table('wwde_tags')->where('id', $sourceId)->delete();
        });

        unset($this->mergeTarget[$sourceId]);
        $this->resetPage();
    }

    /**
     * Simple suggestion engine:
     * - same group
     * - normalized string distance small
     */
    public function getSuggestionsProperty(): array
    {
        if (!$this->showSuggestions) return [];

        // pull all tags once (small enough in your case; if grows, we can scope)
        $tags = DB::table('wwde_tags')->select('id', 'group', 'title')->get();

        $byGroup = $tags->groupBy(fn($t) => (string)$t->group);

        $suggestions = [];

        foreach ($byGroup as $group => $items) {
            $arr = $items->values()->all();
            $n = count($arr);

            for ($i = 0; $i < $n; $i++) {
                for ($j = $i + 1; $j < $n; $j++) {
                    $a = $this->norm($arr[$i]->title);
                    $b = $this->norm($arr[$j]->title);

                    if ($a === $b) {
                        $suggestions[] = [
                            'group' => $group,
                            'source_id' => $arr[$j]->id,
                            'source_title' => $arr[$j]->title,
                            'target_id' => $arr[$i]->id,
                            'target_title' => $arr[$i]->title,
                            'reason' => 'identisch nach Normalisierung',
                            'score' => 100,
                        ];
                        continue;
                    }

                    $dist = levenshtein($a, $b);
                    $maxLen = max(strlen($a), strlen($b)) ?: 1;
                    $similarity = (int) round((1 - ($dist / $maxLen)) * 100);

                    // tune threshold: 85+ catches "golg/golf", "wissenpark/wissenspark"
                    if ($similarity >= 85) {
                        $suggestions[] = [
                            'group' => $group,
                            'source_id' => $arr[$j]->id,
                            'source_title' => $arr[$j]->title,
                            'target_id' => $arr[$i]->id,
                            'target_title' => $arr[$i]->title,
                            'reason' => "ähnlich ({$similarity}%)",
                            'score' => $similarity,
                        ];
                    }
                }
            }
        }

        // best first
        usort($suggestions, fn($x, $y) => $y['score'] <=> $x['score']);

        // cap
        return array_slice($suggestions, 0, 30);
    }

    private function norm(string $s): string
    {
        $s = Str::lower(trim($s));
        $s = str_replace(['&amp;', '&'], ' und ', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return Str::slug($s);
    }

    public function render()
    {
        $query = DB::table('wwde_tags as t')
            ->leftJoin('wwde_location_tag as lt', 't.id', '=', 'lt.tag_id')
            ->select('t.id', 't.group', 't.title', 't.slug', DB::raw('COUNT(lt.location_id) as usage_count'))
            ->groupBy('t.id', 't.group', 't.title', 't.slug');

        if ($this->group) {
            $query->where('t.group', $this->group);
        }

        if (trim($this->q) !== '') {
            $q = trim($this->q);
            $query->where(function ($w) use ($q) {
                $w->where('t.title', 'like', "%{$q}%")
                  ->orWhere('t.slug', 'like', "%{$q}%")
                  ->orWhere('t.group', 'like', "%{$q}%");
            });
        }

        $sortCol = match ($this->sort) {
            'title' => 't.title',
            'group' => 't.group',
            default => 'usage_count',
        };

        $query->orderBy($sortCol, $this->dir === 'asc' ? 'asc' : 'desc');

        $tags = $query->paginate($this->perPage);

        $groups = DB::table('wwde_tags')
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->toArray();

        // for merge target dropdown: keep it simple but usable (filtered list)
        $targetOptions = DB::table('wwde_tags')
            ->select('id', 'group', 'title')
            ->orderBy('group')->orderBy('title')
            ->get();

        return view('livewire.backend.tags.admin-tag-manager', [
            'tags' => $tags,
            'groups' => $groups,
            'targetOptions' => $targetOptions,
        ])->layout('raadmin.layout.master');
    }
}
