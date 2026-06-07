<?php

namespace App\Http\Controllers;

use App\Services\CountryService;

class CountryController extends Controller
{
    public function __construct(
        private CountryService $countryService
    ){}

    public function index()
    {
        try {
            $countries = $this->countryService->getCountries();
            return $this->successResponse(['countries' => $countries]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve countries', 500, $e->getMessage());
        }
    }

    public function store()
    {
        try {
            $this->countryService->storeEeaCountries();
            return $this->successResponse(message: 'Countries imported successfully', statusCode: 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to import countries', 500, $e->getMessage());
        }
    }

    public function destroy()
    {
        try {
            $this->countryService->truncateCountries();
            return $this->successResponse(message: 'Countries truncated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to truncate countries', 500, $e->getMessage());
        }
    }
}
