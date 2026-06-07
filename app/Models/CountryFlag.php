<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryFlag extends Model
{
    protected $fillable = [
        'country_id',
        'emoji',
        'image_url',
        'svg_url',
        'alt_text',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
