<div class="card-footer">
    <div class="row align-items-center flex-column flex-md-row gap-3 gap-md-0">
        <div class="col-12 col-md-3">
            <select wire:model="perPage" class="form-select form-select-sm w-100">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-12 col-md-6">
            <div class="app-pagination-link">
                <ul class="pagination app-pagination justify-content-center mb-0">
                    <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link b-r-left" wire:click="previousPage" href="#">Previous</a>
                    </li>

                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        $range = 2;
                        $start = max(1, $currentPage - $range);
                        $end = min($lastPage, $currentPage + $range);
                    @endphp

                    @for ($i = $start; $i <= $end; $i++)
                        <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                            <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                        </li>
                    @endfor

                    <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link b-r-right" wire:click="nextPage" href="#">Next</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-12 col-md-3 text-center text-md-end">
            <span class="text-muted">
                Zeigt {{ $paginator->firstItem() }} bis {{ $paginator->lastItem() }} von {{ $paginator->total() }} Einträgen
            </span>
        </div>
    </div>
</div>
