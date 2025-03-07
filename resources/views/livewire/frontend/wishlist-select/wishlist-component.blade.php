<div>
<!-- Floating Wishlist Button angepasst für Inline-Display -->
<button wire:click="toggleWishlist" class="wishlist-btn-inline">
    <i class="fa fa-heart"></i>
    @if(count($wishlist) > 0)
        <span class="wishlist-count">{{ count($wishlist) }}</span>
    @endif
</button>

    <!-- Wishlist Overlay -->
    @if($showWishlist)
        <div class="wishlist-overlay" wire:click.self="toggleWishlist">
            <div class="wishlist-box">
                <button class="close-btn" wire:click="toggleWishlist">&times;</button>
                <h3><i class="fa fa-heart text-danger"></i> Meine Wishlist</h3>

                @if(count($locations) > 0)
                    <ul class="wishlist-list">
                        @foreach($locations as $location)
                            <li>
                                <span class="location-name">{{ $location->title }}</span>
                                <button wire:click="removeFromWishlist({{ $location->id }})" class="remove-btn">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <button wire:click="clearWishlist" class="clear-btn">
                        <i class="fa fa-trash"></i> Wishlist leeren
                    </button>

                    @if(count($wishlist) > 1)
                    <button wire:click="compareLocations" class="compare-btn">
                        <i class="fa fa-exchange"></i> Vergleichen
                    </button>
                @endif

                @else
                    <p class="text-muted">Noch keine Locations in der Wishlist.</p>
                @endif
            </div>
        </div>
    @endif
</div>
