<div class="header-search">
    <div class="header-search__form-group position-relative">
        <!-- Suchfeld -->
        <input
            type="text"
            class="header-search__input form-control"
            placeholder="Ort, Land suchen..."
            wire:model.live="searchTerm"
            wire:keydown.arrow-up.prevent="moveHighlight('up')"
            wire:keydown.arrow-down.prevent="moveHighlight('down')"
            wire:keydown.enter.prevent="search"
        />

        <!-- Such-Icon -->
        <span class="header-search__icon">
            <i class="fas fa-search"></i>
        </span>

        <!-- Vorschlagsliste -->
        @if(!empty($suggestions))
            <ul class="header-search__suggestions list-group">
                @foreach($suggestions as $index => $suggestion)
                    <li
                        class="header-search__suggestion list-group-item {{ $highlightedIndex === $index ? 'active' : '' }}"
                        wire:click="selectSuggestion({{ $index }})"
                    >
                        <!-- Titel des Ortes -->
                        <div class="suggestion-title">
                            {{ $suggestion['title'] }}
                        </div>

                        <!-- Details (Land und Kontinent) -->
                        <div class="suggestion-details text-muted">
                            @if(!empty($suggestion['country_title']))
                                {{ $suggestion['country_title'] }}
                            @endif
                            @if(!empty($suggestion['continent_title']))
                                ({{ $suggestion['continent_title'] }})
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

<!-- Styles -->
<style>
.header-search {
    max-width: 600px; /* Breiteres Suchfeld */
    margin: 0 auto;
    position: relative;
}

.header-search__form-group {
    position: relative;
}

.header-search__input {
    width: 100%;
    padding: 10px 40px 10px 15px; /* Platz für das Icon */
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.header-search__input:focus {
    border-color: #0d6efd;
    outline: none;
}

.header-search__icon {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    color: #aaa;
    pointer-events: none;
    font-size: 1.2rem;
}

.header-search__suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-top: 5px;
}

.header-search__suggestion {
    padding: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.header-search__suggestion:hover,
.header-search__suggestion.active {
    background-color: #f8f9fa;
}

.header-search__suggestion.active {
    background-color: #0d6efd;
    color: #fff;
}

.suggestion-title {
    font-weight: 500;
    margin-bottom: 5px; /* Abstand zwischen Titel und Details */
}

.suggestion-details {
    font-size: 0.8rem;
    color: #666;
}
</style>
</div>
