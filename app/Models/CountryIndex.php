<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryIndex extends Model
{
    protected $fillable = [
        'country_id',
        'gini',
        'gini_year',
        'hdi',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
