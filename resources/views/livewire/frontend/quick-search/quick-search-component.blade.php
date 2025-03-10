<div>
    <!-- Fahne (Button) -->
    <div id="sidebarButton" class="p-2 bg-black text-color-white" wire:click="toggleCollapse"
        style="position: fixed; top: 20px; {{ $isCollapsed ? 'left: 0;' : 'left: 300px;' }}">
        <i id="sidebar-arrow" class="fas {{ $isCollapsed ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
    </div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar-left-nav bg-black text-color-white p-4"
        style="{{ $isCollapsed ? 'transform: translateX(-100%);' : 'transform: translateX(0);' }}">
        <h4 class="text-center text-color-white">Suche anpassen</h4>
        <form wire:submit.prevent="redirectToResults">

            <div class="form-group">
                <label for="urlaub">@autotranslate('Urlaub im', app()->getLocale()) <span class="text-danger">*</span></label>
                <select wire:model.change="urlaub" class="form-select py-1">
                    <option value="">@autotranslate('Bitte auswählen', app()->getLocale())</option>
                    @foreach ([1 => 'January', 2 => 'February', 3 => 'März', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'] as $number => $englishMonth)
                        <option value="{{ $number }}">@autotranslate($englishMonth, app()->getLocale())</option>
                    @endforeach
                </select>
                @error('urlaub') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="continent">Kontinent</label>
                <select wire:model.change="continent" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @foreach ($continents as $continent)
                        <option value="{{ $continent->id }}">{{ $continent->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="price">Preis pro Person bis</label>
                <select wire:model.change="price" class="form-select">
                    <option value="">Beliebig</option>
                    @foreach ($ranges as $range)
                        <option value="{{ $range->id }}">{{ $range->Range_to_show }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="sonnenstunden">Sonnenstunden</label>
                <select wire:model.change="sonnenstunden" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @for ($i = 3; $i <= 12; $i++)
                        <option value="more_{{ $i }}">Mindestens {{ $i }} Stunden</option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label for="wassertemperatur">Wassertemperatur</label>
                <select wire:model.change="wassertemperatur" class="form-select py-1">
                    <option value="">Beliebig</option>
                    @for ($i = 16; $i <= 27; $i++)
                        <option value="more_{{ $i }}">Mindestens {{ $i }}°C</option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <input type="checkbox" wire:model.change="nurInBesterReisezeit" id="beste_reisezeit" class="form-check-input">
                <label for="beste_reisezeit">Nur in bester Reisezeit</label>
            </div>

            <h5 class="text-white">Spezielle Wünsche</h5>
            @foreach ($specialWishes as $field => $label)
                <div>
                    <input wire:model.change="spezielle" id="{{ $field }}" type="checkbox"
                        value="{{ $field }}" class="form-check-input">
                    <label for="{{ $field }}">{{ $label }}</label>
                </div>
            @endforeach



            <button type="submit" class="bg-warning text-color-black mt-3 btn py-4 px-1">
                <i class="fas fa-search me-2"></i>
                <span>{{ $filteredLocations }}</span> Ergebnisse von <span>{{ $totalLocations }}</span> anzeigen
            </button>
        </form>


        <div id="icon-section" class="d-flex align-items-center mt-3 custom-icon-group">
            <!-- Detailsuche-Link mit funktionierendem wire:click -->
            <a href="{{ route('detail_search') }}" class="custom-icon-link d-flex align-items-center"
               data-bs-toggle="tooltip" title="Zur Detail-Suche"
               wire:click.prevent="collapseSidebar">
                <i class="fas fa-arrow-circle-right fa-lg me-2"></i> Detailsuche
            </a>
<!-- Reset-Button mit modernerem Mülleimer-Icon -->
<button type="button" wire:click="resetFilters" class="btn custom-icon-btn rounded-circle p-2"
        data-bs-toggle="tooltip" title="Filter zurücksetzen">
    <i class="fas fa-trash-alt fa-lg"></i>
</button>
        </div>

    </div>

    @script
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const isCollapsed = @this.isCollapsed; // Initialzustand aus Livewire

                function initializeSidebarState() {
                    const screenWidth = window.innerWidth;

                    if (screenWidth <= 768 && !isCollapsed) {
                        // Sidebar schließen, wenn sie offen ist
                        @this.dispatch('goOn-Sidebarstate', {
                            state: true
                        });
                    } else if (screenWidth > 768 && isCollapsed) {
                        // Sidebar öffnen, wenn sie geschlossen ist
                        @this.dispatch('goOn-Sidebarstate', {
                            state: false
                        });
                    }
                }

                // Initiale Überprüfung
                initializeSidebarState();
            });
        </script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        Livewire.on("sidebarCollapsed", () => {
            document.getElementById("sidebar").style.transform = "translateX(-100%)";
        });
    });
    </script>

    @endscript



    <style>
        /* Isolierung der Styles für diesen Abschnitt */
        #icon-section {
            display: flex;
            align-items: center;
            gap: 12px; /* Abstand zwischen Icon und Link */
            justify-content: space-between;
            flex-wrap: wrap;
        }

        #icon-section .custom-icon-btn {
            background-color: transparent;
            border: none;
            color: white;
            font-size: 1.3rem;
            padding: 8px;
            transition: color 0.3s ease;
        }

        #icon-section .custom-icon-btn:hover {
            color: #ffc107; /* Bootstrap Warnfarbe */
            text-decoration: none;
        }

        #icon-section .custom-icon-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            font-size: 1rem;
            color: white;
            transition: color 0.3s ease;
        }

        #icon-section .custom-icon-link i {
            font-size: 1.5rem;
            margin-right: 5px;
            color: #ffc107;
            text-decoration: none;
        }

        #icon-section .custom-icon-link:hover {
            color: #ffc107;
            text-decoration: none;
        }
    </style>

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
            width: 100% !important;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .form-group {
            margin-top: 1rem;
        }

        .form-check-input {
            margin-right: 0.5rem;
        }



    </style>
</div>
