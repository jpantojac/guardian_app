<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'description',
        'location', // Geometry
        'status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // We might need a custom accessor for location if we want it as GeoJSON
    // But for now, we'll rely on DB raw queries or a package if installed later.

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at')->where('status', 'verified');
    }
}
