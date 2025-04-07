<div class="trip-activities-wrapper py-4 px-3 bg-light rounded">
    <div class="hero">
        <h1>Entdecke dein nächstes Abenteuer in {{ $locationTitle }}!</h1>
        <p>Tauche ein in die atemberaubenden Seiten dieser Stadt – entdecke verborgene Schätze und plane dein persönliches Abenteuer!</p>
    </div>

    <!-- Filter-Buttons -->
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
        <div class="row justify-content-center">
            @foreach($this->activities as $activity)
                <div class="col-md-6 col-lg-5 mb-4 animate__animated animate__fadeIn">
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
                            @if($tripActivities->pluck('id')->contains($activity['id']))
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

    @if(!empty($tripDays))
        <!-- Trip-Name -->
        <div class="trip-name mt-4 text-center">
            <input
                type="text"
                wire:model.debounce.500ms="tripName"
                class="form-control w-50 mx-auto"
                placeholder="Gib deinem Trip einen Namen..."
                style="font-size: 1.2rem; text-align: center;"
            >
            @if($tripName)
                <h2 class="mt-2">{{ $tripName }}</h2>
            @endif
        </div>

        <!-- Trip-Vorschau -->
        <div class="trip-preview-grid mt-5">
            @foreach($tripDays as $index => $day)
                <div class="trip-day-box day-{{ $index + 1 }} animate__animated animate__fadeIn">
                    <div class="trip-day-header">
                        {{ $day['name'] ?? 'Tag ' . ($index + 1) }}
                        <button wire:click="removeTripDay('{{ $index }}')" class="btn btn-sm btn-outline-danger float-end">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                    <div class="trip-day-content p-3">
                        @forelse($day['activities'] as $activity)
                            <div class="trip-activity-item mb-2 d-flex justify-content-between align-items-center animate__animated animate__fadeIn">
                                <div>
                                    <strong>{{ $activity['title'] }}</strong><br>
                                    <small>Dauer: {{ $activity['duration'] }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button wire:click="moveActivityUp('{{ $activity['id'] }}', '{{ $index }}')" class="btn btn-sm btn-outline-secondary me-1">
                                        <i class="fa-solid fa-arrow-up"></i>
                                    </button>
                                    <button wire:click="moveActivityDown('{{ $activity['id'] }}', '{{ $index }}')" class="btn btn-sm btn-outline-secondary me-1">
                                        <i class="fa-solid fa-arrow-down"></i>
                                    </button>
                                    <select wire:change="moveActivityToDay('{{ $activity['id'] }}', '{{ $index }}', $event.target.value)" class="form-select form-select-sm me-2" style="width: auto;">
                                        <option value="{{ $index }}" selected>Tag {{ $index + 1 }}</option>
                                        @foreach($tripDays as $targetIndex => $targetDay)
                                            @if($targetIndex != $index)
                                                <option value="{{ $targetIndex }}">Tag {{ $targetIndex + 1 }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button wire:click="removeFromTrip('{{ $activity['id'] }}')" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Noch keine Aktivitäten für diesen Tag.</p>
                        @endforelse

                        <div class="trip-notes mt-3">
                            <strong>Notizen:</strong>
                            <textarea
                                wire:model.debounce.500ms="tripDays.{{ $index }}.notes"
                                class="form-control"
                                rows="2"
                                placeholder="Eigene Notizen für Tag {{ $index + 1 }} eingeben..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <button wire:click="addTripDay" class="btn btn-outline-primary">
                <i class="fa-solid fa-plus"></i> Tag hinzufügen
            </button>
        </div>

        <!-- Kurzbeschreibung -->
        <div class="trip-description mt-4">
            <h3>Kurzbeschreibung des Trips</h3>
            <textarea
                wire:model.debounce.500ms="tripDescription"
                class="form-control"
                rows="3"
                placeholder="Beschreibe deinen Trip kurz und prägnant..."
            ></textarea>
            @if($tripDescription)
                <p class="mt-2 text-muted">{{ $tripDescription }}</p>
            @endif
        </div>

        <!-- Buttons -->
        <div class="text-center mt-3">
            <button wire:click="saveTripToLocal" class="btn btn-success" wire:loading.attr="disabled">
                <i class="fa-solid fa-floppy-disk fa-lg"></i>
                <span wire:loading.remove> Trip speichern</span>
                <span wire:loading>Speichert...</span>
            </button>
            <button wire:click="$dispatch('loadTripFromLocalStorage')" class="btn btn-info ms-2" wire:loading.attr="disabled">
                <i class="fa-solid fa-upload fa-lg"></i>
                <span wire:loading.remove> Trip laden</span>
                <span wire:loading>Lädt...</span>
            </button>
            <button wire:click="resetTrip" class="btn btn-danger ms-2">
                <i class="fa-solid fa-rotate-left fa-lg"></i> Trip zurücksetzen
            </button>
        </div>

        <!-- Gespeicherte Daten anzeigen -->
        @if($savedDataPreview)
            <div class="mt-4 text-center">
                <h4>Gespeicherte/Geladene Daten (Debug):</h4>
                <pre class="bg-dark text-white p-3 rounded" style="max-width: 800px; margin: 0 auto; overflow-x: auto;">
                    {{ $savedDataPreview }}
                </pre>
            </div>
        @endif
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success text-center mt-4 animate__animated animate__fadeIn">
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger text-center mt-4 animate__animated animate__fadeIn">
            {{ session('error') }}
        </div>
    @endif

    <style>
        .trip-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .trip-day-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .trip-day-box:hover {
            transform: translateY(-5px);
        }

        .trip-day-header {
            padding: 12px 20px;
            font-weight: bold;
            font-size: 1.2rem;
            color: white;
            background: linear-gradient(135deg, #007bff, #0056b3);
            text-align: center;
        }

        .trip-day-box.day-2 .trip-day-header { background: linear-gradient(135deg, #17a2b8, #117a8b); }
        .trip-day-box.day-3 .trip-day-header { background: linear-gradient(135deg, #ffc107, #e0a800); color: #212529; }
        .trip-day-box.day-4 .trip-day-header { background: linear-gradient(135deg, #dc3545, #b02a37); }

        .trip-activity-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .trip-activity-item:hover {
            background: #e9ecef;
        }

        .trip-notes textarea, .trip-description textarea {
            resize: vertical;
            border-radius: 8px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .trip-description {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .trip-name input {
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .trip-activities-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

        .inspiration-button {
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

        .inspiration-button.active {
            transform: scale(1.06);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .btn-sport.active { background-color: #28a745; border-color: #218838; }
        .btn-freizeitpark.active { background-color: #fd7e14; border-color: #e8590c; }
        .btn-erlebnis.active { background-color: #6b4e9c; border-color: #563d7c; }

        .hero {
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            color: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-success, .btn-info, .btn-danger {
            padding: 10px 20px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-success:hover, .btn-info:hover, .btn-danger:hover {
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
    </style>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('save-trip-local', (event) => {
            const data = event[0];
            localStorage.setItem('tripPlannerData', JSON.stringify(data));
            console.log('Trip gespeichert:', data);
        });

        Livewire.on('loadTripFromLocalStorage', () => {
            if (confirm('Möchtest du den aktuellen Trip überschreiben?')) {
                const saved = localStorage.getItem('tripPlannerData');
                if (saved) {
                    const data = JSON.parse(saved);
                    Livewire.dispatch('loadTripFromLocal', [data]);
                    console.log('Trip geladen:', data);
                } else {
                    alert('Kein gespeicherter Trip gefunden.');
                }
            }
        });

        Livewire.on('trip-loaded', () => {
            console.log('Trip wurde erfolgreich geladen und UI aktualisiert.');
        });
    });
</script>

</div>
