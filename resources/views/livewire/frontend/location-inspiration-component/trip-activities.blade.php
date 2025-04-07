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
        <!-- Aktivitäten-Liste -->
        <div class="row justify-content-center">
            @foreach($this->activities as $activity)
                <div class="col-md-6 col-lg-5 mb-4">
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

    <!-- Trip-Vorschau -->
    @if(!empty($tripDays))
        <div class="trip-preview-grid mt-5">
            @foreach($tripDays as $index => $day)
                <div class="trip-day-box day-{{ $index + 1 }}">
                    <div class="trip-day-header">
                        {{ $day['name'] ?? 'Tag ' . ($index + 1) }}
                        <button wire:click="removeTripDay('{{ $index }}')" class="btn btn-sm btn-outline-danger float-end">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                    <div class="trip-day-content p-3">
                        @forelse($day['activities'] as $activity)
                            <div class="trip-activity-item mb-2 d-flex justify-content-between align-items-center">
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

        <!-- Kurzbeschreibung unter dem Trip -->
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
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success text-center mt-4">
            {{ session('success') }}
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .trip-day-header {
            padding: 12px 20px;
            font-weight: bold;
            font-size: 1.2rem;
            color: white;
            background: #007bff;
            text-align: center;
        }

        .trip-day-box.day-2 .trip-day-header { background: #17a2b8; }
        .trip-day-box.day-3 .trip-day-header { background: #ffc107; color: #212529; }
        .trip-day-box.day-4 .trip-day-header { background: #dc3545; }

        .trip-activity-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
        }

        .trip-activity-item button, .trip-activity-item select {
            margin-left: 10px;
        }

        .trip-notes {
            margin-top: 20px;
        }

        .trip-notes textarea {
            resize: vertical;
        }

        .trip-description {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .trip-description h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .trip-description textarea {
            resize: vertical;
        }

        .trip-activities-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

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

        .inspiration-button.active {
            transform: scale(1.06);
            position: relative;
            z-index: 2;
        }

        .btn-sport.active {
            border: 2px solid #28a745;
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.3), 0 0 12px rgba(40, 167, 69, 0.7);
            background-color: #218838;
        }

        .btn-freizeitpark.active {
            border: 2px solid #fd7e14;
            box-shadow: 0 0 0 4px rgba(253, 126, 20, 0.3), 0 0 12px rgba(253, 126, 20, 0.7);
            background-color: #e8590c;
        }

        .btn-erlebnis.active {
            border: 2px solid #6b4e9c;
            box-shadow: 0 0 0 4px rgba(107, 78, 156, 0.3), 0 0 12px rgba(107, 78, 156, 0.7);
            background-color: #563d7c;
        }

        .inspiration-button.active:not(.btn-sport):not(.btn-freizeitpark):not(.btn-erlebnis) {
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2), 0 0 12px rgba(255, 255, 255, 0.5);
            background-color: #495057;
        }

        .btn-erlebnis { background-color: #6b4e9c; }
        .btn-sport { background-color: #28a745; }
        .btn-freizeitpark { background-color: #fd7e14; }

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

        .btn-warning:hover {
            background-color: #ffc107;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.6);
        }
    </style>
</div>
