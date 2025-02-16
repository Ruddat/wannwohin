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
                @else
                    <p class="text-muted">Noch keine Locations in der Wishlist.</p>
                @endif
            </div>
        </div>
    @endif


<!-- Wishlist Styles -->
<style>
/* Wishlist Button - In einer Reihe mit Kreis */
.wishlist-btn-inline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: white;
    border: 2px solid red;
    border-radius: 50%;
    font-size: 22px;
    color: red;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.wishlist-btn-inline:hover {
    background: red;
    color: white;
}

/* Wishlist Zähler */
.wishlist-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: red;
    color: white;
    font-size: 12px;
    border-radius: 50%;
    padding: 3px 7px;
    font-weight: bold;
}

/* Container für die korrekte Zeilenanordnung */
.custom-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.custom-dropdown,
.custom-suggestion {
    flex: 1;
}


    /* Overlay Styling */
    .wishlist-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease-in-out;
        z-index: 9999;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Wishlist Box */
    .wishlist-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        width: 350px;
        text-align: center;
        position: relative;
        box-shadow: 0px 5px 15px rgba(0,0,0,0.3);
        transform: scale(0.95);
        animation: zoomIn 0.3s ease-in-out forwards;
    }

    @keyframes zoomIn {
        from { transform: scale(0.95); }
        to { transform: scale(1); }
    }

    /* Schließen Button */
    .close-btn {
        background: none;
        border: none;
        font-size: 20px;
        position: absolute;
        top: 10px;
        right: 15px;
        cursor: pointer;
        color: #777;
    }

    .close-btn:hover {
        color: red;
    }

    /* Wishlist Liste */
    .wishlist-list {
        list-style: none;
        padding: 0;
        max-height: 300px;
        overflow-y: auto;
    }

    .wishlist-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 16px;
    }

    .location-name {
        flex-grow: 1;
        text-align: left;
    }

    .remove-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: red;
        font-size: 16px;
    }

    .remove-btn:hover {
        color: darkred;
    }

    /* Wishlist leeren */
    .clear-btn {
        margin-top: 15px;
        background: red;
        color: white;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        width: 100%;
    }

    .clear-btn:hover {
        background: darkred;
    }



</style>
</div>
