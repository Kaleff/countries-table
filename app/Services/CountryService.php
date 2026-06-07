<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CountryFlag;
use App\Models\CountryIndex;
use App\Traits\UseCountryApi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CountryService
{
    use UseCountryApi;

    private $eeaAcronyms = ['EFTA', 'EU'];

    public function storeEeaCountries()
    {
        $contriesData = $this->fetchEeaCountries();
        DB::transaction(function () use ($contriesData) {
            foreach ($contriesData as $countryData) {
                $country = Country::updateOrCreate(
                    ['cca3' => $countryData['cca3'], 'cca2' => $countryData['cca2']],
                    [
                        'name' => Arr::get($countryData, 'name.common'),
                        'official_name' => Arr::get($countryData, 'name.official'),
                    ]
                );
                if (isset($countryData['flag'])) {
                    CountryFlag::updateOrCreate(
                        ['country_id' => $country->id],
                        [
                            'emoji' => Arr::get($countryData, 'flag.emoji'),
                            'image_url' => Arr::get($countryData, 'flag.png'),
                            'svg_url' => Arr::get($countryData, 'flag.svg'),
                            'alt_text' => Arr::get($countryData, 'flag.alt'),
                        ]
                    );
                }
                $hdiValue = Arr::get($countryData, 'hdi');
                $hdiValue = is_numeric($hdiValue) && $hdiValue <= 1 ? $hdiValue : null;
                CountryIndex::updateOrCreate(
                    ['country_id' => $country->id],
                    [
                        'gini' => Arr::get($countryData, 'gini.0.value'),
                        'gini_year' => Arr::get($countryData, 'gini.0.year'),
                        'hdi' => $hdiValue,
                    ]
                );
            }
        });
    }

    public function truncateCountries()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            CountryIndex::truncate();
            CountryFlag::truncate();
            Country::truncate();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    private function fetchEeaCountries()
    {
        $url = $this->europeanCountriesUrl();
        $response = $this->getRequest($url);
        $countriesData = $response->json();
        $countriesData = array_filter($countriesData, function ($country) {
            return isset($country['regionalBlocs']) && 
                collect($country['regionalBlocs'])->pluck('acronym')->intersect($this->eeaAcronyms)->isNotEmpty();
        });
        return $countriesData;
    }
}