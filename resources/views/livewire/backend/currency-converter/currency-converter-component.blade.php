<div>
    <!-- Button to open the modal -->
<!-- Währungsrechner Button -->
<button wire:click="openModal" class="electric-button">
    <i class="fas fa-exchange-alt"></i> @autotranslate('Rechner', app()->getLocale())
    <span class="arrow-container">
        <span class="arrow-icon">➜</span>
    </span>
</button>

    <!-- Modal -->
    @if ($showModal)
        <div class="modal-backdrop fade show"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">@autotranslate('Währungsrechner', app()->getLocale())</h5>
                        <button type="button" class="close" wire:click="closeModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
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
                </div>
            </div>
        </div>
    @endif





<style>
    /* styles.css oder in deinem Stylesheet */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
}

.modal {
    z-index: 1050;
}

.modal-content {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    padding: 1rem;
}

.modal-title {
    margin: 0;
}

.modal-body {
    padding: 1rem;
}

.close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}
</style>
</div>
