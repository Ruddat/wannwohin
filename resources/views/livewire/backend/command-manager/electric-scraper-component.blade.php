<div>
    <button wire:click="runScraper" class="btn btn-primary">Scraper starten</button>

    @if (session()->has('message'))
        <div class="alert alert-success mt-3">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    @endif

    @if ($output)
        <div class="mt-3">
            <h5>Command Output:</h5>
            <pre>{{ $output }}</pre>
        </div>
    @endif
</div>
