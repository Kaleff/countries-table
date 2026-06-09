<?php

namespace App\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'gini' => $this->gini,
            'gini_year' => $this->gini_year,
            'hdi' => $this->hdi,
            'gini_rating' => $this->giniRating(),
        ];
    }

    /**
     * Classify the Gini coefficient into an income-equality rating.
     *
     * - below 30        => good   (green)
     * - 30 to 36        => ok     (green with a little yellow / lime)
     * - above 36        => so-so  (yellow)
     *
     * @return array{level: string, label: string, color: string}|null
     */
    private function giniRating(): ?array
    {
        if ($this->gini === null) {
            return null;
        }

        $gini = (float) $this->gini;

        return match (true) {
            $gini < 30 => ['level' => 'good', 'label' => 'Good', 'color' => 'green'],
            $gini <= 36 => ['level' => 'ok', 'label' => 'OK', 'color' => 'lime'],
            default => ['level' => 'so-so', 'label' => 'So-so', 'color' => 'yellow'],
        };
    }
}
