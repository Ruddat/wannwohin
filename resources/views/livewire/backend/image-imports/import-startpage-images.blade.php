<div>
    <h1>Startpage Image Import</h1>
    <p>Um Bilder zu importieren, müssen die Dateien die folgenden Anforderungen erfüllen:</p>
    <ul>
        <li>Die Bilder müssen im Ordner <code>img/startpage</code> gespeichert sein.</li>
        <li>Die Dateien sollten nach folgendem Schema benannt sein:</li>
        <ul>
            <li><strong>Hintergrundbild:</strong> <code>01_beste_reisezeit_b.webp</code> oder <code>01_beste_reisezeit_b.jpg</code></li>
            <li><strong>Hauptbild:</strong> <code>01_beste_reisezeit_s.webp</code> oder <code>01_beste_reisezeit_s.jpg</code></li>
            <li><strong>Textdatei (optional):</strong> <code>01_beste_reisezeit.txt</code></li>
        </ul>
        <li>Die Präfixe <code>_b</code> und <code>_s</code> kennzeichnen das Hintergrund- bzw. Hauptbild.</li>
        <li>Die Dateien müssen die gleiche Basis (z. B. <code>01_beste_reisezeit</code>) teilen.</li>
    </ul>
    <p>Beispiel: Für das Thema "Beste Reisezeit" sollten folgende Dateien im Ordner vorhanden sein:</p>
    <ul>
        <li><code>01_beste_reisezeit_b.webp</code> oder <code>01_beste_reisezeit_b.jpg</code></li>
        <li><code>01_beste_reisezeit_s.webp</code> oder <code>01_beste_reisezeit_s.jpg</code></li>
        <li><code>01_beste_reisezeit.txt</code> (optional)</li>
    </ul>

    <!-- Import-Button -->
    <button wire:click="importImagesAndText" class="btn btn-primary">Bilder importieren</button>
    <button wire:click="importImagesAndText" class="btn btn-primary">Bilder importieren</button>
    
    <!-- Statusmeldung -->
    @if ($message)
        <div class="alert alert-info mt-3">
            {!! $message !!}
        </div>
    @endif

    <!-- Erfolgsnachricht -->
    <div wire:loading wire:target="importImagesAndText" class="mt-3">
        <p>Import läuft, bitte warten...</p>
    </div>
</div>
