<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'upz_code',
        'locality_name',
        'boundary', // Geometry MultiPolygon
    ];

    public function alerts()
    {
        return $this->hasMany(ZoneAlert::class);
    }
}
