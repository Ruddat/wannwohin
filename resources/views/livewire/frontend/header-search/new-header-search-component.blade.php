<div class="header-search">
    <div class="header-search__form-group position-relative">
        <input
            type="text"
            class="header-search__input form-control"
            placeholder="Urlaubsziel suchen"
            wire:model.live="searchTerm"
            wire:keydown.arrow-up.prevent="moveHighlight('up')"
            wire:keydown.arrow-down.prevent="moveHighlight('down')"
            wire:keydown.enter.prevent="search"
        />

        <span class="header-search__icon">
            <i class="fas fa-search"></i>
        </span>

        @if(!empty($suggestions))
            <ul class="header-search__suggestions list-group">
                @foreach($suggestions as $index => $suggestion)
                    <li
                        class="header-search__suggestion list-group-item {{ $highlightedIndex === $index ? 'active' : '' }}"
                        wire:click="$set('searchTerm', '{{ $suggestion['title'] }}')"
                    >
                        {{ $suggestion['title'] }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>


<style>
/* Container für die gesamte Suche */
.header-search {
    max-width: 450px;
    margin: 0 auto;
    position: relative;
}

/* Eingabefeld Styling */
.header-search__input {
    padding: 12px 40px 12px 15px; /* Platz für das Icon einrechnen */
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 30px; /* Abgerundete Ecken */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Schatten */
    transition: all 0.3s ease-in-out;
}

/* Hover- und Fokus-Effekte für das Eingabefeld */
.header-search__input:focus {
    outline: none;
    border-color: #ffc107; /* Gelbe Farbe beim Fokus */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); /* Stärkerer Schatten */
}

/* Such-Icon im Eingabefeld */
.header-search__icon {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    color: #ffc107; /* Gelbe Farbe für Konsistenz */
    font-size: 1.2rem;
    pointer-events: none; /* Kein Klick möglich */
}

/* Vorschlagsliste */
.header-search__suggestions {
    position: absolute;
    top: 110%; /* Etwas Abstand vom Eingabefeld */
    left: 0;
    right: 0;
    z-index: 10;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px; /* Abgerundete Ecken */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-height: 250px; /* Maximale Höhe */
    overflow-y: auto;
    padding: 0;
    margin: 0;
}

/* Einzelner Vorschlag */
.header-search__suggestion {
    padding: 12px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Hover- und aktive Vorschläge */
.header-search__suggestion:hover,
.header-search__suggestion.active {
    background-color: #ffc107;
    color: #fff;
}

/* Abstände optimieren */
.header-search__suggestion:last-child {
    border-bottom: none;
}
</style>
</div>
