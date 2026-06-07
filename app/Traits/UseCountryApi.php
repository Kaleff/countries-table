<?php

namespace App\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait UseCountryApi
{
    private $countryApiBaseUrl = 'https://restcountries.com/v4';
    private $europeSubRoute = '/region/europe';
    private $fieldsQuery = '?fields=';
    private $countryFields = 'cca2,cca3,name,gini,hdi,flag,regionalBlocs,subregion';

    private function europeanCountriesUrl(): string
    {
        return $this->countryApiBaseUrl . $this->europeSubRoute 
            . $this->fieldsQuery . $this->countryFields;
    }

    private function getRequest(string $url): Response
    {
        return Http::connectTimeout(10)
            ->timeout(60)
            ->retry(3, 2000, throw: false)
            ->get($url);
    }
}
