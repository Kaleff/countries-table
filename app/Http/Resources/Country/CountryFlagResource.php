<?php

namespace App\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryFlagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'emoji' => $this->emoji,
            'image_url' => $this->image_url,
            'svg_url' => $this->svg_url,
            'alt_text' => $this->alt_text,
        ];
    }
}
