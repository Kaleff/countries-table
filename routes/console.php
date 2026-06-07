<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\CountryService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('countries:import', function () {
    $this->comment('Importing country data from REST Countries API...');
    $countryService = app()->make(CountryService::class);
    $countryService->storeEeaCountries();
})->purpose('Import country data from REST Countries API');

Artisan::command('countries:truncate', function () {
    $this->comment('Truncating country data...');
    $countryService = app()->make(CountryService::class);
    $countryService->truncateCountries();
})->purpose('Truncate country data');