<?php

namespace App\Http\Controllers\Frontend\Search;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Search\DetailSearchService;

class DetailSearchV2Controller extends Controller
{
    protected $searchService;

    public function __construct(DetailSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $filters = $request->all();

        // Live-Ergebniszähler mit AJAX
        if ($request->ajax()) {
            return response()->json([
                'count' => $this->searchService->getResultCount($filters),
                'preview' => $this->searchService->getPreviewResults($filters, 5)
            ]);
        }

        return view('pages.detailSearch.v2.index', [
            'filterOptions' => $this->searchService->getFilterOptions(),
            'initialCount' => $this->searchService->getResultCount($filters),
            'appliedFilters' => $filters
        ]);
    }

    public function results(Request $request)
    {
        $filters = $request->all();
        $results = $this->searchService->getResults($filters);

        return view('pages.detailSearch.v2.results', [
            'results' => $results,
            'total' => $results->total(),
            'appliedFilters' => $filters
        ]);
    }
}
