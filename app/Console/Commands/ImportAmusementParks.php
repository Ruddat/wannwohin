<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AmusementParkService;

class ImportAmusementParks extends Command
{
    protected $signature = 'parks:import';
    protected $description = 'Import amusement parks from Wartezeiten.APP and Queue-Times APIs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(AmusementParkService $service)
    {
        $this->info('Importing amusement parks...');
        try {
            $service->importParksToDatabase();
            $this->info('Amusement parks imported successfully!');
        } catch (\Exception $e) {
            $this->error("Failed to import parks: {$e->getMessage()}");
        }
    }
}
