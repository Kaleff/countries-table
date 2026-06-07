<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'official_name',
        'cca3',
        'cca2',
    ];

    public function flag()
    {
        return $this->hasOne(CountryFlag::class);
    }

    public function index()
    {
        return $this->hasOne(CountryIndex::class);
    }
}
