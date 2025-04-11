<div class="p-3 bg-light rounded shadow-sm">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h5 class="mb-3 text-black">🎢 Deine Bewertung</h5>

    <!-- Sterne -->
    <div class="mb-3 fs-4 text-warning">
        @for ($i = 1; $i <= 5; $i++)
            <i class="bi bi-star{{ $i <= $rating ? '-fill' : '' }}"
               wire:click="$set('rating', {{ $i }})"
               style="cursor: pointer;"></i>
        @endfor
        <span class="text-muted ms-2 fs-6 text-black">({{ $rating }} von 5)</span>
    </div>

    <!-- Kommentar -->
    <textarea wire:model.defer="comment"
              class="form-control form-control-sm mb-3"
              rows="2"
              placeholder="Optionaler Kommentar ..."></textarea>

    <button class="btn btn-warning w-100 mb-4" wire:click="submitFeedback">
        Absenden
    </button>

    @if (session()->has('coolnessMessage'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('coolnessMessage') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h5 class="mb-3 text-black">😎 Coolness-Faktor</h5>

    <!-- Slider -->
    <input type="range"
           class="form-range"
           min="1" max="10" step="1"
           wire:model="coolness">

    <p class="text-center small text-black">Aktuell: <strong>{{ $coolness }} / 10</strong></p>

    <button class="btn btn-info w-100" wire:click="submitCoolness">
        Abstimmen
    </button>
</div>
