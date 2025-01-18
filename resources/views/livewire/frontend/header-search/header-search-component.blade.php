<div class="header-search">
    <div class="header-search__form-group position-relative">
        <input
            type="text"
            class="header-search__input form-control"
            placeholder="Ort suchen..."
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
                    wire:click="selectSuggestion({{ $index }})"
                >
                    {{ $suggestion['title'] }}
                </li>
            @endforeach
        </ul>
        @endif
    </div>


<style>
.header-search {
    max-width: 400px;
    margin: 0 auto;
    position: relative;
}

.header-search__form-group {
    position: relative;
}

.header-search__icon {
    position: absolute;
    top: 50%;
    right: 10px;
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
    z-index: 10;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
}

.header-search__suggestion {
    padding: 10px;
    cursor: pointer;
}

.header-search__suggestion:hover,
.header-search__suggestion.active {
    background-color: #f8f9fa;
}

.list-group-item.active {
    z-index: 2;
    /* color: #fff; */
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>
</div>
