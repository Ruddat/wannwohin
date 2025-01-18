<div>
    <h1>Urlaubsbilder Importieren</h1>
    <p>Die Urlaubsbilder müssen im Ordner <code>public/img/location_main_img/{kontinent}/{land}/{location}/urlaubsfotos</code> gespeichert sein.</p>
    <ul>
        <li><strong>Kontinent:</strong> Europa, Asien, etc.</li>
        <li><strong>Land:</strong> Deutschland, Italien, etc.</li>
        <li><strong>Location:</strong> Berlin, Mailand, etc.</li>
    </ul>
    <p>Beispielstruktur:</p>
    <pre>
    public/img/location_main_img/europa/deutschland/berlin/urlaubsfotos
    </pre>

    <button wire:click="importLocationImages" class="btn btn-primary">Bilder importieren</button>

    @if ($message)
        <div class="alert alert-info mt-3">
            {!! $message !!}
        </div>
    @endif

    <div wire:loading wire:target="importLocationImages" class="mt-3">
        <p>Import läuft, bitte warten...</p>
    </div>
</div>
