<div>
    <div x-data="{ open: @entangle('showModal') }">
        <!-- Button mit dem electric-button Stil -->
        <button @click="open = true" class="electric-button">
            <i class="fas fa-exchange-alt"></i> <!-- Icon hinzufügen -->
            @autotranslate('Währungsrechner', app()->getLocale())
        </button>

        <!-- Modal -->
        <div x-show="open" x-cloak>
            <div class="modal-backdrop fade show"></div>
            <div class="modal fade show d-block" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">@autotranslate('Währungsrechner', app()->getLocale())</h5>
                            <button type="button" class="close" @click="open = false">
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
        </div>
    </div>

    <style>
        /* Electric Button Stil */
        .electric-button {
            background: linear-gradient(to bottom, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: bold;
            font-size: 12px; /* Kleinere Schrift */
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1); /* Weniger dominanter Schatten */
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .electric-button:hover {
            transform: translateY(-1px);
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.15);
        }

        .electric-button i {
            margin-right: 5px;
            font-size: 14px; /* Kleinere Icon-Größe */
        }

        /* Pfeil-Container */
        .arrow-container {
            width: 22px; /* Kleinere Kreisgröße */
            height: 22px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            box-shadow: 0px 2px 3px rgba(0, 0, 0, 0.2);
            position: relative;
            top: -1px;
            transform: rotate(5deg);
        }

        /* Pfeil-Styling */
        .arrow-icon {
            font-size: 12px; /* Kleinere Pfeilgröße */
            color: #007bff;
            transition: transform 0.3s ease-in-out;
            display: inline-block;
        }

        /* Animation bei Hover */
        .electric-button:hover .arrow-container {
            transform: translateX(3px) rotate(5deg);
            box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.25);
        }

        /* Modal-Stile */
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
