<div class="card p-3">
    <h4 class="fw-bold text-center">@autotranslate('Währungsrechner', app()->getLocale())</h4>

    <div class="mb-3">
        <label>@autotranslate('Betrag', app()->getLocale())</label>
        <input type="number" wire:model="amount" class="form-control" min="1">
    </div>

    <div class="mb-3 d-flex justify-content-between">
        <div>
            <label>@autotranslate('Von', app()->getLocale())</label>
            <select wire:model="fromCurrency" class="form-control">
                @foreach($currencies as $currency)
                    <option value="{{ $currency }}">{{ $currency }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>@autotranslate('Nach', app()->getLocale())</label>
            <select wire:model="toCurrency" class="form-control">
                @foreach($currencies as $currency)
                    <option value="{{ $currency }}"
                        {{ $currency == $toCurrency ? 'selected' : '' }}>
                        {{ $currency }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <button wire:click="convertCurrency" class="btn btn-primary w-100 mt-3">
        @autotranslate('Umrechnen', app()->getLocale())
    </button>

    @if ($convertedAmount !== null)
        <div class="alert alert-info mt-3 text-center">
            <strong>{{ number_format($convertedAmount, 2, ',', '.') }} {{ $toCurrency }}</strong>
        </div>
    @endif
</div>
