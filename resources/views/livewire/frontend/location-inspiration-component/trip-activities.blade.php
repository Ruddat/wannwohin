
<div class="py-4 px-3 bg-light rounded">
    <div class="hero">
        <h1>Entdecke dein nächstes Abenteuer in Berlin!</h1>
        <p>Tauche ein in die atemberaubenden Seiten dieser Stadt – entdecke verborgene Schätze und plane dein persönliches Abenteuer!</p>
    </div>

    <h2 class="text-center text-primary mb-4">Was kann man in {{ $locationTitle }} machen?</h2>

    <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
        @foreach($this->activities as $activity)
        @php
            $btnClass = match($activity['category']) {
                'erlebnis' => 'btn-erlebnis',
                'sport' => 'btn-sport',
                'ki' => 'btn-ki',
                default => 'btn-secondary'
            };
        @endphp

        <button
            wire:click="selectActivity('{{ $activity['id'] }}')"
            class="inspiration-button {{ $btnClass }} {{ $selectedActivity === $activity['id'] ? 'active' : '' }}"
        >
            <i class="fa-solid {{ $activity['icon'] }}"></i> {{ $activity['title'] }}
        </button>
    @endforeach
    </div>


    <div class="row justify-content-center">
        @foreach($this->activities as $activity)
            @if($selectedActivity === $activity['id'])
                <div class="col-md-6 col-lg-5">
                    <x-activity-card
                    :title="$activity['title']"
                    :description="$activity['description']"
                    :category="ucfirst($activity['category'])"
                    :icon="$activity['icon']"
                    :duration="$activity['duration']"
                    :location="'In der Nähe'"
                    :rating="$activity['rating']"
                >
                    <x-slot name="buttons">
                        @if($selectedActivity === $activity['id'])
                            <button class="btn btn-success btn-sm">
                                <i class="fa-solid fa-check"></i> Im Trip!
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
            @endif
        @endforeach
    </div>


    @if (session()->has('success'))
        <div class="alert alert-success text-center mt-4">
            {{ session('success') }}
        </div>
    @endif
<!-- In der Blade-Datei oder im Layout -->


<style>
    .btn-purple {
        background-color: #8e44ad;
        color: white;
    }
    .btn-purple:hover {
        background-color: #732d91;
        color: white;
    }

    .inspiration-button {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 999px;
    font-weight: 500;
    font-size: 0.95rem;
    color: #fff;
    border: none;
    box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.inspiration-button i {
    margin-right: 0.5rem;
    font-size: 1rem;
}

/* Kategorie-Farben */
.btn-erlebnis   { background-color: #8e44ad; } /* Lila */
.btn-sport      { background-color: #27ae60; } /* Grün */
.btn-ki         { background-color: #3498db; } /* Blau */
.btn-secondary  { background-color: #7f8c8d; }  /* Grau */

.inspiration-button:hover {
    transform: scale(1.05);
}

.hero {
    background: linear-gradient(135deg, #0f172a, #1e3a8a);
    color: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    width: 100%;
    box-sizing: border-box;
}

</style>
</div>
