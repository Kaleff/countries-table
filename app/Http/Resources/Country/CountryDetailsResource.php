<?php

namespace App\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'official_name' => $this->official_name,
            'cca3' => $this->cca3,
            'cca2' => $this->cca2,
            'flag' => new CountryFlagResource($this->whenLoaded('flag')),
            'index' => new CountryIndexResource($this->whenLoaded('index')),
        ];
    }
}
