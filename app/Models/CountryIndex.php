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

    protected $casts = [
        'gini' => 'decimal:2',
        'hdi' => 'decimal:3',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
