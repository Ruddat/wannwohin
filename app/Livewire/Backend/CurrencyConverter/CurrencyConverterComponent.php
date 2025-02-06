<?php

namespace App\Livewire\Backend\CurrencyConverter;

use Livewire\Component;
use App\Models\ModCurrencyExchangeRate;

class CurrencyConverterComponent extends Component
{
    public $amount = 1;
    public $fromCurrency = 'EUR';  // Standardwert Euro
    public $toCurrency;  // Zielwährung aus der Location
    public $convertedAmount = null;
    public $currencies = [];

    public function mount($toCurrency = 'USD') // Standard: USD, falls keine Währung übergeben wird
    {
        $this->toCurrency = $toCurrency;

        // Lade alle verfügbaren Währungen aus der Datenbank
        $this->currencies = ModCurrencyExchangeRate::distinct()
            ->pluck('base_currency')
            ->toArray();
    }

    public function convertCurrency()
    {
        $exchangeRate = ModCurrencyExchangeRate::where('base_currency', $this->fromCurrency)
            ->where('target_currency', $this->toCurrency)
            ->first();

        if ($exchangeRate) {
            $this->convertedAmount = round($this->amount * $exchangeRate->exchange_rate, 2);
        } else {
            $this->convertedAmount = null;
        }
    }

    public function render()
    {
        return view('livewire.backend.currency-converter.currency-converter-component', [
            'currencies' => $this->currencies
        ]);
    }
}
