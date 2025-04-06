<div class="py-4 px-3 bg-light rounded">
    <div class="hero">
        <h1>Entdecke dein nächstes Abenteuer in {{ $locationTitle }}!</h1>
        <p>Tauche ein in die atemberaubenden Seiten dieser Stadt – entdecke verborgene Schätze und plane dein persönliches Abenteuer!</p>
    </div>

    <!-- Filter-Buttons basierend auf uschrift -->
    <div class="d-flex flex-wrap gap-3 justify-content-center mb-4">
        @foreach($this->activityFilters as $filter)
            <button
                wire:click="toggleActivity('{{ $filter['title'] }}')"
                class="inspiration-button {{ $filter['btnClass'] }} {{ in_array($filter['title'], $selectedActivities) ? 'active' : '' }}"
            >
                <i class="fa-solid {{ $filter['icon'] }}"></i>
                {{ $filter['title'] }}
            </button>
        @endforeach
    </div>

    @if(empty($selectedActivities))
        <div class="text-center text-muted mb-4">
            <p>Wähle eine oder mehrere Aktivitäten, um Details zu sehen!</p>
        </div>
    @else
        <!-- Aktivitäten -->
        <div class="row justify-content-center">
            @foreach($this->activities as $activity)
                <div class="col-md-6 col-lg-5">
                    <x-activity-card
                        :title="$activity['title']"
                        :description="$activity['description']"
                        :category="$activity['category']"
                        :icon="$activity['icon']"
                        :image="$activity['image']"
                        :duration="$activity['duration']"
                        :location="'In der Nähe'"
                        :rating="$activity['rating']"
                    >
                        <x-slot name="buttons">
                            @if(in_array($activity, $tripActivities))
                                <button class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-check"></i> Im Trip!
                                </button>
                                <button wire:click="removeFromTrip('{{ $activity['id'] }}')" class="btn btn-danger btn-sm ms-2">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @else
                                <button wire:click="addToTrip('{{ $activity['id'] }}')" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-plus"></i> Zum Trip hinzufügen
                                </button>
                            @endif
                            @if($activity['isRecommended'])
                                <span class="badge bg-success align-self-center ms-2">Empfohlen 🤖</span>
                            @endif
                        </x-slot>
                    </x-activity-card>
                </div>
            @endforeach
        </div>
    @endif
    <div class="trip-map-container mb-4">
        <svg id="tripMapGraphic" width="100%" height="500" viewBox="0 0 800 500" style="background-color: #f9f9f9; border-radius: 12px;"></svg>
    </div>
    <!-- Trip-Vorschau -->
    @if(!empty($tripActivities))
        <div class="trip-preview mt-4 p-3 bg-white rounded shadow-sm">
            <h3>Dein Trip</h3>
            <ul class="list-group">
                @foreach($tripActivities as $tripActivity)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $tripActivity['title'] }} ({{ $tripActivity['duration'] }})
                        <button wire:click="removeFromTrip('{{ $tripActivity['id'] }}')" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success text-center mt-4">
            {{ session('success') }}
        </div>
    @endif

    <style>
        .inspiration-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 1rem;
            color: #fff;
            border: 2px solid transparent;
            background-color: #6c757d;
            transition: all 0.3s ease;
        }

        .inspiration-button i {
            margin-right: 8px;
        }

        .inspiration-button:hover,
        .inspiration-button.active {
            border-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .btn-erlebnis { background-color: #6b4e9c; }
        .btn-sport { background-color: #28a745; }
        .btn-freizeitpark { background-color: #6b4e9c; }
        .btn-secondary { background-color: #6c757d; }

        .hero {
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            color: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .card {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .trip-preview {
            border: 1px solid #ddd;
        }
    </style>


<script>
    window.addEventListener('trip-map-update', event => {
    console.log('Trip map update received:', event.detail);
    const { items, center } = event.detail || {};
    if (Array.isArray(items) && items.length > 0 && center) {
        window.renderTripMap(items, center);
    } else {
        console.log('Event data invalid:', items, center);
    }
});



    document.addEventListener("livewire:load", () => {
        window.renderTripMap = (mapItems, center) => {
    console.log('Rendering map with items:', mapItems, 'Center:', center);
    const svg = document.getElementById('tripMapGraphic');
    svg.innerHTML = '';

    const centerX = 400;
    const centerY = 250;
    const radius = 200;

    // Mittelpunkt
    const origin = document.createElementNS("http://www.w3.org/2000/svg", "circle");
    origin.setAttribute("cx", centerX);
    origin.setAttribute("cy", centerY);
    origin.setAttribute("r", 12);
    origin.setAttribute("fill", "#0d6efd");
    svg.appendChild(origin);

    const youText = document.createElementNS("http://www.w3.org/2000/svg", "text");
    youText.setAttribute("x", centerX);
    youText.setAttribute("y", centerY + 4);
    youText.setAttribute("text-anchor", "middle");
    youText.setAttribute("fill", "white");
    youText.textContent = "Du";
    svg.appendChild(youText);

    if (!mapItems || mapItems.length === 0) {
        console.log('No items to render');
        return;
    }

    mapItems.forEach((item, index) => {
        console.log('Rendering item:', item);
        const angle = (360 / mapItems.length) * index * (Math.PI / 180);
        const x = centerX + radius * Math.cos(angle);
        const y = centerY + radius * Math.sin(angle);

        const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
        line.setAttribute("x1", centerX);
        line.setAttribute("y1", centerY);
        line.setAttribute("x2", x);
        line.setAttribute("y2", y);
        line.setAttribute("stroke", "#ccc");
        svg.appendChild(line);

        const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        circle.setAttribute("cx", x);
        circle.setAttribute("cy", y);
        circle.setAttribute("r", 10);
        circle.setAttribute("fill", "#6b4e9c");
        svg.appendChild(circle);

        const label = document.createElementNS("http://www.w3.org/2000/svg", "text");
        label.setAttribute("x", x);
        label.setAttribute("y", y - 15);
        label.setAttribute("text-anchor", "middle");
        label.setAttribute("font-size", "12");
        label.setAttribute("fill", "#000");
        label.textContent = item.title;
        svg.appendChild(label);

        const dist = document.createElementNS("http://www.w3.org/2000/svg", "text");
        dist.setAttribute("x", x);
        dist.setAttribute("y", y + 20);
        dist.setAttribute("text-anchor", "middle");
        dist.setAttribute("font-size", "10");
        dist.setAttribute("fill", "#555");
        dist.textContent = `${item.distance} km`;
        svg.appendChild(dist);
    });
};
    });
</script>
<script>
    window.addEventListener('trip-map-update', event => {
        const { items, center } = event.detail || {};
if (Array.isArray(items) && items.length > 0 && center) {
    window.renderTripMap(items, center);
}
    });
</script>









</div>
