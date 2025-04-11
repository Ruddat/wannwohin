<div>
    <!-- Floating Indicator Button -->
    <button wire:click="toggleFavorites"
    class="favorites-btn-inline {{ count($favorites) > 0 ? 'has-favorites' : '' }}">
    <img src="{{ asset('img/tripplan.png') }}" alt="Trip Plan" class="trip-icon" />

    @if(count($favorites) > 0)
        <span class="favorites-count">{{ count($favorites) }}</span>
    @endif
</button>

    <!-- Favorites Overlay -->
    @if($showFavorites)
        <div class="favorites-overlay" wire:click.self="toggleFavorites">
            <div class="favorites-box">
                <button class="close-btn" wire:click="toggleFavorites">×</button>
                <h3><i class="fa fa-map text-primary"></i> Mein Trip-Plan</h3> <!-- Geändert zu fa-map -->

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(count($favorites) > 0)
                @foreach($this->groupedFavorites as $location => $activities)
                <h5 class="mt-3 mb-2">
                    <i class="fa fa-map-marker-alt text-primary me-1"></i> {{ $location }}
                </h5>
                <ul class="favorites-list">
                    @foreach($activities as $activity)
                        <li>
                            <span class="activity-name">{{ $activity['title'] }}</span>
                            <button wire:click="removeFromFavorites('{{ $activity['id'] }}')" class="remove-btn">
                                <i class="fa fa-trash"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endforeach
                    <button wire:click="clearFavorites" class="clear-btn">
                        <i class="fa fa-trash"></i> Trip-Plan leeren
                    </button>
                    <button
    wire:click="openTripPlanner"
    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-2 w-full"
>
    <i class="fa fa-edit"></i> Trip bearbeiten
</button>
                    @else
                    <p class="text-muted">Noch keine Aktivitäten im Trip-Plan.</p>
                @endif
            </div>
{{--
            <pre>{{ print_r(session('favorite_activities'), true) }}</pre>
--}}
        </div>

    @endif




<style>
.favorites-btn-inline {
    /* nicht mehr fixed */
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    font-size: 20px;
    position: relative; /* statt fixed */

}

.favorites-btn-inline.has-favorites {
    background-color: #28a745 !important; /* grün wenn Favoriten vorhanden */
}

.favorites-btn-inline:hover {
    background-color: #3399ff; /* Helleres Blau beim Hover */
    transform: scale(1.1);
    transition: all 0.2s ease-in-out;
    box-shadow: 0 4px 8px rgba(0,0,0,0.4);
}

.trip-icon {
    width: 28px;
    height: 28px;
    object-fit: contain;
    pointer-events: none; /* Damit Klick nur auf Button wirkt */
}

.favorites-btn-inline:hover .trip-icon {
    transform: scale(1.1);
    transition: transform 0.2s ease-in-out;
}

.favorites-btn-inline.has-favorites:hover {
    background-color: #45c767 !important; /* Helleres Grün */
}

.favorites-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #fff;
    color: #007bff;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

    .favorites-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .favorites-box {
        background: white;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 400px;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        border: none;
        background: none;
        font-size: 24px;
        cursor: pointer;
    }

    .favorites-list {
        list-style: none;
        padding: 0;
    }

    .favorites-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .activity-name {
        font-weight: 500;
    }

    .remove-btn {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
    }

    .clear-btn {
        width: 100%;
        margin-top: 10px;
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
    }

    .clear-btn:hover {
        background-color: #c82333;
    }

    .favorites-box h5 {
    border-bottom: 1px solid #ddd;
    padding-bottom: 4px;
    margin-top: 20px;
    font-size: 16px;
}


</style>
</div>
