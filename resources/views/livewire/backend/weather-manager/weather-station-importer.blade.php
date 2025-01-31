<div class="container mt-4">
    <h3>Weather Station Importer</h3>

    <p>
        Führe den Command aus, um Wetterstationen herunterzuladen, in die Datenbank zu importieren und den nächstgelegenen Stationen zuzuweisen.
    </p>

    <!-- Button mit wire:loading -->
    <button wire:click="runCommand" class="btn btn-primary d-flex align-items-center justify-content-center" @if($isRunning) disabled @endif>
        <span wire:loading.remove>
            <i class="bi bi-arrow-clockwise me-2"></i>
            Command ausführen
        </span>
        <span wire:loading>
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Läuft...
        </span>
    </button>

    <!-- Konsolenausgabe -->
    <div class="mt-4">
        <h5>Konsolenausgabe:</h5>
        <pre class="bg-dark text-white p-3 border rounded" style="max-height: 300px; overflow-y: auto; font-size: 0.9rem;">
            {{ $output }}
        </pre>
    </div>
</div>
