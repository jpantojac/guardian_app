<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'category_id',
        'threshold',
        'time_window_hours',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
