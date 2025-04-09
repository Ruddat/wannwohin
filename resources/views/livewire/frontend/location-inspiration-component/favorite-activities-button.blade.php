<button wire:click="toggleFavorite"
        class="btn btn-sm {{ $isFavorite ? 'btn-danger' : 'btn-primary' }}">
    <i class="fa-solid {{ $isFavorite ? 'fa-map-pin' : 'fa-map' }}"></i>
    {{ $isFavorite ? 'Entfernen' : 'Zum Trip' }}
</button>
