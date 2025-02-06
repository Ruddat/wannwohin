<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CurrencyApiService;

class UpdateCurrencyExchangeRates extends Command
{
    protected $signature = 'currency:update-exchange-rates';
    protected $description = 'Aktualisiert alle Wechselkurse einmal im Monat';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $apiService = new CurrencyApiService();
        $success = $apiService->updateAllExchangeRates();

        if ($success) {
            $this->info('Alle Wechselkurse wurden erfolgreich aktualisiert.');
        } else {
            $this->error('Fehler beim Aktualisieren der Wechselkurse.');
        }
    }
}
