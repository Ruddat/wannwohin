<div class="trip-activities-wrapper py-5 px-4 bg-gradient-to-br from-blue-50 to-green-50 rounded-xl shadow-xl">

    <!-- Hero-Bereich -->
<div class="hero relative overflow-hidden rounded-lg">
    @if(!empty($location->text_pic2))
    <div class="inset-0 bg-cover bg-center opacity-40"
         style="background-image: url('{{ \Illuminate\Support\Str::startsWith($location->text_pic2, ['http', '/']) ? $location->text_pic2 : asset('storage/' . $location->text_pic2) }}');">
    </div>
@else
    <div class="absolute inset-0 bg-cover bg-center opacity-40"
         style="background-image: url('https://source.unsplash.com/1200x400/?travel,{{ $locationTitle }}');">
    </div>
@endif

    <div class="relative z-10 p-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white drop-shadow-lg">Entdecke dein nächstes Abenteuer in {{ $locationTitle }}!</h1>
        <p class="text-lg md:text-xl text-gray-200 drop-shadow-md mt-2">Plane deinen perfekten Trip mit versteckten Highlights!</p>

        @if(!empty($location->image_short_text))
            <div class="mt-4 text-sm text-gray-300 max-w-2xl mx-auto">
                {{ strip_tags($location->image_short_text) }}
            </div>
        @endif
    </div>
</div>
    <!-- Filter-Buttons -->
<!-- Abstand nach Hero -->
<div class="filter-buttons flex flex-wrap gap-3 justify-center mt-8 mb-10">
    @foreach($this->activityFilters as $filter)
        <button
            wire:click="toggleActivity('{{ $filter['title'] }}')"
            class="inspiration-button {{ $filter['btnClass'] }} {{ in_array($filter['title'], $selectedActivities) ? 'active' : '' }}"
        >
            <i class="fa-solid {{ $filter['icon'] }} mr-2"></i>
            {{ $filter['title'] }}
        </button>
    @endforeach
</div>
    <!-- Aktivitäten-Karten -->

    @if(empty($selectedActivities))
        <div class="text-center text-gray-600 mb-6">
            <p>Wähle Aktivitäten aus, um loszulegen!</p>
        </div>
    @else
        <div class="row justify-content-center mt-3">
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
                                <button wire:click="removeFromTrip('{{ $activity['id'] }}')" class="btn btn-danger btn-sm ms-2 trash-btn" title="Aktivität entfernen">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            @else
                                <button wire:click="addToTrip('{{ $activity['id'] }}')" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-plus"></i> Zum Trip
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
        <div class="trip-name mt-8 text-center">
            <input
                type="text"
                wire:model.debounce.500ms="tripName"
                class="custom-input w-50 mx-auto bg-white shadow-md border-0"
                placeholder="Gib deinem Trip einen Namen..."
            >
            @if($tripName)
                <h2 class="mt-3 text-3xl font-semibold text-gray-800">{{ $tripName }}</h2>
            @endif
        </div>

        <!-- Fortschrittsbalken -->
        <div class="total-duration mt-6 max-w-md mx-auto">
            <h3 class="text-xl font-semibold text-gray-700">Gesamtdauer: {{ $this->totalDuration['hours'] }} Stunden</h3>
            <div class="progress-bar bg-gray-200 rounded-full h-4 mt-2 overflow-hidden">
                <div class="progress-fill bg-gradient-to-r from-pink-400 to-purple-500 h-full rounded-full" style="width: {{ $this->totalDuration['percentage'] }}%;"></div>
            </div>
        </div>

        <!-- Trip-Vorschau -->
        <div class="trip-preview-grid mt-8 mb-3">
            @foreach($tripDays as $index => $day)
                <div class="trip-day-box day-{{ $index + 1 }} animate__animated animate__fadeInUp draggable-droppable" data-day-index="{{ $index }}">
                    <div class="trip-day-header flex justify-between items-center">
                        <span><i class="fa-solid fa-calendar-day mr-2"></i>{{ $day['name'] ?? 'Tag ' . ($index + 1) }}</span>
                        <button wire:click="removeTripDay('{{ $index }}')" class="btn btn-sm btn-outline-danger trash-btn" title="Tag entfernen">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                    <div class="trip-day-content p-4">
                        @forelse($day['activities'] as $activity)
                            <div class="trip-activity-item mb-3 p-3 rounded-lg shadow-sm draggable" draggable="true" data-activity-id="{{ $activity['id'] }}" data-day-index="{{ $index }}">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <i class="fa-solid {{ strtolower($activity['category']) === 'wassersport' ? 'fa-water' : (strtolower($activity['category']) === 'klettern' ? 'fa-mountain' : (strtolower($activity['category']) === 'radfahren' ? 'fa-bicycle' : (strtolower($activity['category']) === 'essen und trinken' ? 'fa-utensils' : 'fa-star'))) }} text-purple-500 mr-2"></i>
                                        <div>
                                            <strong class="text-gray-800">{{ $activity['title'] }}</strong><br>
                                            <small class="text-gray-600">Dauer: {{ $activity['duration'] }}</small>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="moveActivityUp('{{ $activity['id'] }}', '{{ $index }}')" class="custom-btn btn-outline-secondary" title="Nach oben">
                                            <i class="fa-solid fa-arrow-up"></i>
                                        </button>
                                        <button wire:click="moveActivityDown('{{ $activity['id'] }}', '{{ $index }}')" class="custom-btn btn-outline-secondary" title="Nach unten">
                                            <i class="fa-solid fa-arrow-down"></i>
                                        </button>
                                        <select wire:change="moveActivityToDay('{{ $activity['id'] }}', '{{ $index }}', $event.target.value)" class="custom-select me-2 bg-white shadow-sm">
                                            <option value="{{ $index }}" selected>Tag {{ $index + 1 }}</option>
                                            @foreach($tripDays as $targetIndex => $targetDay)
                                                @if($targetIndex != $index)
                                                    <option value="{{ $targetIndex }}">Tag {{ $targetIndex + 1 }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button wire:click="removeFromTrip('{{ $activity['id'] }}')" class="custom-btn btn-danger trash-btn" title="Aktivität entfernen">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic">Noch keine Aktivitäten – zieh welche hierher!</p>
                        @endforelse

                        <div class="trip-notes mt-3 w-full">
                            <strong class="text-gray-700">Notizen:</strong>
                            <textarea
                                wire:model.debounce.500ms="tripDays.{{ $index }}.notes"
                                class="custom-textarea bg-white shadow-sm border-0 w-full"
                                rows="2"
                                placeholder="Notizen für Tag {{ $index + 1 }}..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-6">
            <button wire:click="addTripDay" class="custom-btn btn-outline-primary shadow-md">
                <i class="fa-solid fa-plus"></i> Tag hinzufügen
            </button>
        </div>

        <!-- Kurzbeschreibung -->
        <div class="trip-description mt-6 max-w-2xl mx-auto text-center">
            <h3 class="text-2xl font-semibold text-gray-800">Kurzbeschreibung</h3>
            <textarea
                wire:model.debounce.500ms="tripDescription"
                class="custom-textarea bg-white shadow-md border-0"
                rows="3"
                placeholder="Beschreibe deinen Trip..."
            ></textarea>
            @if($tripDescription)
                <p class="mt-2 text-gray-600">{{ $tripDescription }}</p>
            @endif
        </div>

        <!-- Buttons -->
        <div class="text-center mt-6 flex justify-center space-x-4">
            <button wire:click="saveTripToLocal" class="custom-btn btn-success shadow-lg" wire:loading.attr="disabled">
                <i class="fa-solid fa-floppy-disk fa-lg"></i>
                <span wire:loading.remove> Speichern</span>
                <span wire:loading>Speichert...</span>
            </button>
            <button wire:click="$dispatch('loadTripFromLocalStorage')" class="custom-btn btn-info shadow-lg" wire:loading.attr="disabled">
                <i class="fa-solid fa-upload fa-lg"></i>
                <span wire:loading.remove> Laden</span>
                <span wire:loading>Lädt...</span>
            </button>
            <button wire:click="exportToPDF" class="custom-btn btn-purple shadow-lg" wire:loading.attr="disabled">
                <i class="fa-solid fa-file-pdf fa-lg"></i>
                <span wire:loading.remove> Als PDF exportieren</span>
                <span wire:loading>Exportiert...</span>
            </button>
            <!-- Neuer PDF-Share-Button -->
    <button wire:click="sharePDF" class="custom-btn btn-purple shadow-lg" wire:loading.attr="disabled">
        <i class="fa-solid fa-share-alt fa-lg"></i>
        <span wire:loading.remove> PDF teilen</span>
        <span wire:loading>Teilt...</span>
    </button>
    <button wire:click="shareViaWhatsApp" class="custom-btn btn-success shadow-lg" wire:loading.attr="disabled">
        <i class="fa-brands fa-whatsapp fa-lg"></i>
        <span wire:loading.remove> Via WhatsApp teilen</span>
        <span wire:loading>Teilt...</span>
    </button>

            <button wire:click="resetTrip" class="custom-btn btn-danger shadow-lg">
                <i class="fa-solid fa-rotate-left fa-lg"></i> Zurücksetzen
            </button>
            @if(!empty($lastRemoved))
                <button wire:click="undoRemove" class="custom-btn btn-warning shadow-lg">
                    <i class="fa-solid fa-undo fa-lg"></i> Rückgängig
                </button>
            @endif
        </div>

        <!-- Debug-Daten -->
        @if($savedDataPreview)
            <div class="mt-6 text-center">
                <h4 class="text-xl font-semibold text-gray-700">Gespeicherte/Geladene Daten (Debug):</h4>
                <pre class="bg-gray-900 text-white p-4 rounded-lg shadow-md max-w-3xl mx-auto overflow-x-auto">
                    {{ $savedDataPreview }}
                </pre>
            </div>
        @endif
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success text-center mt-4 animate__animated animate__fadeIn shadow-md">
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger text-center mt-4 animate__animated animate__fadeIn shadow-md">
            {{ session('error') }}
        </div>
    @endif

    <style>
    /* Basis-Styles */
    .trip-activities-wrapper {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
        background: linear-gradient(135deg, #f0f9ff 0%, #e6f7ed 100%);
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    /* Hero-Bereich */
    .hero {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(30, 58, 138, 0.8));
    }

    .hero .absolute {
    background-color: #333;
    background-blend-mode: multiply;
}

    .hero .relative {
        padding: 4rem 2rem;
        position: relative;
        z-index: 10;
        text-align: center;
    }

    .hero h1 {
        font-size: 2.5rem;
        line-height: 1.2;
        font-weight: 800;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        margin-bottom: 0.5rem;
    }

    .hero p {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.9);
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }


    /* Filter-Buttons */
        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .inspiration-button {
            padding: 8px 16px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            border: 2px solid transparent;
            background-color: #6b7280;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
        }

        .inspiration-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border-color: #ff00ff;
        }

        .inspiration-button.active {
            transform: scale(1.06);
            box-shadow: 0 0 15px rgba(255, 0, 255, 0.6);
        }

        .btn-sport.active { background-color: #a7f3d0; border-color: #10b981; }
        .btn-freizeitpark.active { background-color: #fed7aa; border-color: #f97316; }
        .btn-erlebnis.active { background-color: #d8b4fe; border-color: #a855f7; }

    /* Fortschrittsbalken */
    .total-duration {
        max-width: 500px;
        margin: 2rem auto;
    }

    .total-duration h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    text-align: center;
}

    .progress-bar {
        background-color: #e5e7eb;
        border-radius: 9999px;
        height: 0.75rem;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 9999px;
        background: linear-gradient(90deg, #ec4899 0%, #8b5cf6 100%);
        transition: width 0.5s ease;
    }

        /* Trip-Grid */
        .trip-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            padding: 0 16px;
        }

        .trip-day-box {
            background: #f9fafb;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .trip-day-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .trip-day-header {
            padding: 14px 24px;
            font-weight: 700;
            font-size: 1.25rem;
            color: white;
            background: linear-gradient(135deg, #a5b4fc, #6366f1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .trip-day-box.day-2 .trip-day-header { background: linear-gradient(135deg, #6ee7b7, #34d399); }
        .trip-day-box.day-3 .trip-day-header { background: linear-gradient(135deg, #f9a8d4, #ec4899); color: #1f2937; }
        .trip-day-box.day-4 .trip-day-header { background: linear-gradient(135deg, #fdba74, #f97316); }

        .trip-day-content {
            padding: 16px;
        }

        .trip-activity-item {
            background: #ffffff;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: move;
            border: 1px solid #e5e7eb;
        }

        .trip-activity-item:hover {
            background: #f3f4f6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #ff00ff;
        }

        .trip-activity-item.dragging {
            opacity: 0.5;
            background: #d1d5db;
        }

        /* Custom Inputs und Buttons */
        .custom-input {
            border-radius: 12px;
            padding: 12px;
            font-size: 1.2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .custom-input:focus {
            box-shadow: 0 0 10px rgba(236, 72, 153, 0.5);
            border-color: #ec4899;
        }

        .custom-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    background-color: white;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    font-size: 0.875rem;
    line-height: 1.5;
}

.custom-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    ring-width: 2px;
    ring-color: #3b82f6;
}

        .custom-btn {
            padding: 8px 16px;
            font-size: 0.9rem;
            border-radius: 9999px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .custom-btn.btn-outline-secondary {
            background-color: #e5e7eb;
            color: #374151;
            border: none;
        }

        .custom-btn.btn-outline-secondary:hover {
            background-color: #d1d5db;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }

        .custom-btn.btn-danger {
            background-color: #f87171;
            color: white;
            border: none;
        }

        .custom-btn.btn-danger:hover {
            background-color: #ef4444;
            box-shadow: 0 0 8px rgba(255, 0, 255, 0.5);
        }

        .custom-btn.btn-success {
            background-color: #6ee7b7;
            color: #1f2937;
        }

        .custom-btn.btn-success:hover {
            background-color: #34d399;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }

        .custom-btn.btn-info {
            background-color: #a5b4fc;
            color: #1f2937;
        }

        .custom-btn.btn-info:hover {
            background-color: #6366f1;
            box-shadow: 0 0 8px rgba(99, 102, 241, 0.5);
        }

        .custom-btn.btn-warning {
            background-color: #fdba74;
            color: #1f2937;
        }

        .custom-btn.btn-warning:hover {
            background-color: #f97316;
            box-shadow: 0 0 8px rgba(249, 115, 22, 0.5);
        }

        .custom-btn.btn-purple {
            background-color: #d8b4fe;
            color: #1f2937;
        }

        .custom-btn.btn-purple:hover {
            background-color: #a855f7;
            box-shadow: 0 0 8px rgba(168, 85, 247, 0.5);
        }

        .custom-select {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            border: none;
            background-color: #f3f4f6;
            transition: all 0.3s ease;
        }

        .custom-select:focus {
            box-shadow: 0 0 8px rgba(236, 72, 153, 0.5);
            background-color: #fff;
            border-color: #ec4899;
        }

        /* Trash-Button */
        .trash-btn {
            position: relative;
            overflow: hidden;
            border: none;
            transition: all 0.3s ease;
        }

        .trash-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(255, 0, 255, 0.5);
        }

        .trash-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 150%;
            height: 150%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.3s ease;
        }

        .trash-btn:hover::before {
            transform: translate(-50%, -50%) scale(1);
        }

        /* Drag-and-Drop */
        .draggable-droppable.drag-over {
            border: 2px dashed #a855f7;
            background: rgba(168, 85, 247, 0.1);
        }

        i.fa-solid {
            margin-right: 8px;
        }

        /* Responsive Anpassungen */
    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2rem;
        }

        .hero p {
            font-size: 1rem;
        }

        .filter-buttons {
            gap: 0.5rem;
        }

        .inspiration-button {
            min-width: 120px;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .custom-input {
            width: 90%;
        }

        .trip-preview-grid {
            grid-template-columns: 1fr;
        }
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

        // Drag-and-Drop Logik
        let draggedItem = null;

        document.addEventListener('dragstart', (e) => {
            const draggable = e.target.closest('.draggable');
            if (draggable) {
                draggedItem = draggable;
                draggedItem.classList.add('dragging');
                e.dataTransfer.setData('text/plain', draggable.dataset.activityId);
                e.dataTransfer.setData('fromDay', draggable.dataset.dayIndex);
            }
        });

        document.addEventListener('dragend', (e) => {
            if (draggedItem) {
                draggedItem.classList.remove('dragging');
                draggedItem = null;
            }
            document.querySelectorAll('.draggable-droppable').forEach(el => el.classList.remove('drag-over'));
        });

        document.addEventListener('dragover', (e) => {
            e.preventDefault();
        });

        document.addEventListener('dragenter', (e) => {
            const droppable = e.target.closest('.draggable-droppable');
            if (droppable) {
                droppable.classList.add('drag-over');
            }
        });

        document.addEventListener('dragleave', (e) => {
            const droppable = e.target.closest('.draggable-droppable');
            if (droppable) {
                droppable.classList.remove('drag-over');
            }
        });

        document.addEventListener('drop', (e) => {
            e.preventDefault();
            const droppable = e.target.closest('.draggable-droppable');
            if (droppable && draggedItem) {
                const activityId = e.dataTransfer.getData('text/plain');
                const fromDay = parseInt(e.dataTransfer.getData('fromDay'));
                const toDay = parseInt(droppable.dataset.dayIndex);
                if (fromDay !== toDay) {
                    Livewire.dispatch('dragDropActivity', [activityId, fromDay, toDay]);
                }
            }
            document.querySelectorAll('.draggable-droppable').forEach(el => el.classList.remove('drag-over'));
        });
    });
</script>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('share-pdf', (event) => {
            const fileUrl = event[0].fileUrl;
            if (navigator.share) {
                fetch(fileUrl)
                    .then(response => response.blob())
                    .then(blob => {
                        const file = new File([blob], "Trip.pdf", { type: "application/pdf" });
                        navigator.share({
                            files: [file],
                            title: 'Mein Trip-Plan',
                            text: 'Schau dir meinen geplanten Trip an!',
                        }).catch(err => console.log('Teilen fehlgeschlagen:', err));
                    });
            } else {
                alert('Dein Browser unterstützt das Teilen nicht. Du kannst das PDF manuell herunterladen.');
            }
        });
    });
</script>
<script>
    Livewire.on('open-url', (event) => {
        window.open(event[0].url, '_blank');
    });
</script>

</div>
