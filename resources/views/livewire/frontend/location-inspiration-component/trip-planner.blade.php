<div>
    @if($isVisible)
        <div class="modal fade show d-block" id="tripPlannerModal" tabindex="-1" aria-labelledby="modalHeaderTitle" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h1 class="modal-title fs-5" id="modalHeaderTitle">
                            <i class="fas fa-map me-2 text-primary"></i>Trip Planner
                        </h1>
                        <button wire:click="hideModal" type="button" class="btn-close" aria-label="Schließen"></button>
                    </div>

                    <div class="modal-body">
                        @if(session('success'))
                            <div class="alert alert-success mb-4">{{ session('success') }}</div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-bold">Wie möchtest du deinen Trip speichern?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model.change="useDays" value="0" id="planAsList">
                                <label class="form-check-label" for="planAsList">Nur als Liste speichern</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model.change="useDays" value="1" id="planWithDays">
                                <label class="form-check-label" for="planWithDays">In Tagespläne unterteilen</label>
                            </div>
                        </div>

                        {{-- 📍 Location-Anzeige --}}
                        @php
                            $firstLocation = null;
                            if ($useDays) {
                                foreach ($tripDays as $day) {
                                    if (!empty($day['activities'][0]['location_name'])) {
                                        $firstLocation = $day['activities'][0]['location_name'];
                                        break;
                                    }
                                }
                            } else {
                                $firstLocation = $tripActivities[0]['location_name'] ?? null;
                            }
                        @endphp

                        @if($firstLocation)
                            <div class="alert alert-light border d-flex align-items-center mb-3">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <span><strong>Ort:</strong> {{ $firstLocation }}</span>
                            </div>
                        @endif

                        @if($useDays)
                            {{-- Tagesbasiert --}}
                            @foreach($tripDays as $index => $day)
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="card-title mb-0">{{ $day['name'] }}</h5>
                                            @if(count($tripDays) > 1)
                                                <button wire:click="removeTripDay({{ $index }})" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <textarea wire:model="tripDays.{{ $index }}.notes" wire:change="updateDayNotes({{ $index }}, $event.target.value)" class="form-control mb-3" rows="2" placeholder="Notizen für {{ $day['name'] }}"></textarea>
                                        @if(!empty($day['activities']))
                                            <ul class="list-group list-group-flush">
                                                @foreach($day['activities'] as $activity)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ $activity['title'] }}</span>
                                                        <button wire:click="removeFromTrip('{{ $activity['id'] }}')" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted fst-italic">Keine Aktivitäten in diesem Tag.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-flex gap-2">
                                <button wire:click="addTripDay" class="btn btn-primary w-100"><i class="fas fa-plus me-2"></i>Tag hinzufügen</button>
                                <button wire:click="saveTrip" class="btn btn-success w-100"><i class="fas fa-save me-2"></i>Speichern</button>
                            </div>
                        @else
                            {{-- Listenbasiert --}}
                            @if(!empty($tripActivities))
                            @php
                            $groupedActivities = collect($tripActivities)
                                ->groupBy('location_name')
                                ->sortKeys(); // nach Städten sortiert
                        @endphp

                        @foreach($groupedActivities as $city => $activities)
                            <h6 class="text-primary mt-3 mb-1">
                                <i class="fas fa-map-marker-alt me-1 text-warning"></i> {{ $city }}
                            </h6>
                            <ul class="list-group mb-2">
                                @foreach($activities as $activity)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $activity['title'] }}</span>
                                        <button wire:click="removeFromActivities('{{ $activity['id'] }}')" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                            @else
                                <p class="text-muted fst-italic">Noch keine Aktivitäten in deinem Trip.</p>
                            @endif
                            <button wire:click="saveTrip" class="btn btn-success w-100 mt-2"><i class="fas fa-save me-2"></i>Speichern</button>
                            <button wire:click="saveTripToDatabase" class="btn btn-outline-primary w-100 mt-2">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Trip veröffentlichen
                            </button>

                            @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
