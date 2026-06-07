<?php

namespace App\Http\Controllers;

use App\Http\Requests\Country\IndexRequest;
use App\Http\Resources\Country\CountryDetailsResource;
use App\Services\CountryService;
use Illuminate\Support\Arr;

class CountryController extends Controller
{
    public function __construct(
        private CountryService $countryService
    ){}

    public function index(IndexRequest $request)
    {
        try {
            $sortBy = $request->validated('sort_by', 'name');
            $sortOrder = $request->validated('sort_order', 'asc');
            $countries = $this->countryService->getCountries($sortBy, $sortOrder);
            $resourceData = CountryDetailsResource::collection($countries)
                ->response($request)
                ->getData(true);
            $resourceData['countries'] = Arr::pull($resourceData, 'data');
            return $this->successResponse(data: $resourceData);
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
