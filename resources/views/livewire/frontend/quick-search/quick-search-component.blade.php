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
                <select wire:model="price" class="form-select">
                    <option value="">Beliebig</option>
                    @foreach($ranges as $range)
                        <option value="{{ $range->id }}">{{ $range->Range_to_show }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="urlaub">Urlaub im</label>
                <select wire:model="urlaub" class="form-select py-1">
                    @foreach($months as $key => $month)
                        <option value="{{ $key }}">{{ $month }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="sonnenstunden">Sonnenstunden</label>
                <select wire:model="sonnenstunden" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @for($i = 3; $i <= 12; $i++)
                        <option value="more_{{ $i }}">min. {{ $i }}h</option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label for="wassertemperatur">Wassertemperatur</label>
                <select wire:model="wassertemperatur" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @for($i = 16; $i <= 27; $i++)
                        <option value="more_{{ $i }}">min. {{ $i }}°C</option>
                    @endfor
                </select>
            </div>

            <h5 class="text-white">Spezielle Wünsche</h5>
            <div>
                <input wire:model="spezielle" id="Beliebig" type="radio" value="" class="form-check-input">
                <label for="Beliebig">Beliebig</label>
            </div>
            <div>
                <input wire:model="spezielle" id="Strandurlaub" type="radio" value="list_beach" class="form-check-input">
                <label for="Strandurlaub">Strandurlaub</label>
            </div>
            <div>
                <input wire:model="spezielle" id="Städtereise" type="radio" value="list_citytravel" class="form-check-input">
                <label for="Städtereise">Städtereise</label>
            </div>
            <div>
                <input wire:model="spezielle" id="Sport" type="radio" value="list_sports" class="form-check-input">
                <label for="Sport">Sporturlaub</label>
            </div>

            <button type="submit" class="bg-warning text-color-black mt-3 btn py-4 px-1">
                <i class="fas fa-search me-2"></i>
                <span>{{ $filteredLocations }}</span> von <span>{{ $totalLocations }}</span> Ergebnissen anzeigen
            </button>
        </form>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Funktion zur Überprüfung der Bildschirmgröße
    function checkScreenSize() {
        const screenWidth = window.innerWidth;

        if (screenWidth <= 768) {
            Livewire.dispatch('setSidebarState', { state: true }); // Sidebar einklappen
        } else {
            Livewire.dispatch('setSidebarState', { state: false }); // Sidebar ausklappen
        }
    }

    // Initiale Prüfung
    checkScreenSize();

    // Überwache Fenstergröße
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
