<div>
    <!-- Fahne (Button) -->
    <div id="sidebarButton"
         class="p-2 bg-black text-color-white"
         wire:click="toggleCollapse"
         style="position: fixed; top: 20px; {{ $isCollapsed ? 'left: 0;' : 'left: 300px;' }}">
        <i id="sidebar-arrow" class="fas {{ $isCollapsed ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
    </div>

    <!-- Sidebar -->
    <div id="sidebar"
         class="sidebar-left-nav bg-black text-color-white p-4"
         style="{{ $isCollapsed ? 'transform: translateX(-100%);' : 'transform: translateX(0);' }}">
        <h4 class="text-center text-color-white">Suche anpassen</h4>
        <form wire:submit.prevent="redirectToResults">
            <div class="form-group">
                <label for="continent">Kontinent</label>
                <select wire:model.change="continent" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @foreach($continents as $continent)
                        <option value="{{ $continent->id }}">{{ $continent->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="price">Preis pro Person bis</label>
                <select wire:model.change="price" class="form-select">
                    <option value="">Beliebig</option>
                    @foreach($ranges as $range)
                        <option value="{{ $range->id }}">{{ $range->Range_to_show }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="urlaub">Urlaub im</label>
                <select wire:model.change="urlaub" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @foreach([
                        'Januar', 'Februar', 'März', 'April', 'Mai',
                        'Juni', 'Juli', 'August', 'September',
                        'Oktober', 'November', 'Dezember'
                    ] as $month)
                        <option value="{{ $month }}">{{ $month }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="sonnenstunden">Sonnenstunden</label>
                <select wire:model.change="sonnenstunden" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @for($i = 3; $i <= 12; $i++)
                    <option value="more_{{ $i }}">Mindestens {{ $i }} Stunden</option>
                @endfor
                </select>
            </div>

            <div class="form-group">
                <label for="wassertemperatur">Wassertemperatur</label>
                <select wire:model.change="wassertemperatur" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @for($i = 16; $i <= 27; $i++)
                        <option value="more_{{ $i }}">Mindestens {{ $i }}°C</option>
                    @endfor
                </select>
            </div>

            <h5 class="text-white">Spezielle Wünsche</h5>
            @foreach([
                'list_beach' => 'Strandurlaub',
                'list_citytravel' => 'Städtereise',
                'list_sports' => 'Sporturlaub',
                'list_culture' => 'Kulturreise',
                'list_nature' => 'Natururlaub',
                'list_watersport' => 'Wassersport',
                'list_wintersport' => 'Wintersport',
                'list_mountainsport' => 'Bergsport',
                'list_amusement_park' => 'Freizeitpark',
            ] as $field => $label)
                <div>
                    <input wire:model.change="spezielle" id="{{ $field }}" type="checkbox" value="{{ $field }}" class="form-check-input">
                    <label for="{{ $field }}">{{ $label }}</label>
                </div>
            @endforeach

            <button type="submit" class="bg-warning text-color-black mt-3 btn py-4 px-1">
                <i class="fas fa-search me-2"></i>
                <span>{{ $filteredLocations }}</span> Ergebnisse von <span>{{ $totalLocations }}</span> anzeigen
            </button>
        </form>

        <div class="mt-2">
            <a class="text-decoration-underline text-white" href="{{ route('detail_search') }}"><i class="fas fa-arrow-circle-right text-warning rounded-circle me-1 bg-white"></i>Detailsuche</a>
        </div>
    </div>


    <script>
        document.addEventListener('livewire:initialized', () => {
            function checkScreenSize() {
                const screenWidth = window.innerWidth;

                // Sidebar automatisch schließen oder öffnen
                if (screenWidth <= 768) {
                    // Event "goOn-Delete" für Einklappen senden
                    @this.dispatch('goOn-Sidebarstate', { state: true });
                } else {
                    // Event "goOn-Delete" für Ausklappen senden
                    @this.dispatch('goOn-Sidebarstate', { state: false });
                }
            }

            // Initiale Überprüfung bei Seitenladen
            checkScreenSize();

            // Überwachung der Fenstergröße bei Änderung
            window.addEventListener('resize', checkScreenSize);
        });
    </script>



    <style>
        #sidebarButton {
            cursor: pointer;
            z-index: 1000;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 300px;
            z-index: 999;
            transition: transform 0.3s ease;
        }

        #sidebar-arrow {
            font-size: 1.5rem;
        }

        .form-select {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .form-group {
            margin-top: 1rem;
        }

        .form-check-input {
            margin-right: 0.5rem;
        }

        .btn {
            width: 100%;
        }


</style>
</div>
