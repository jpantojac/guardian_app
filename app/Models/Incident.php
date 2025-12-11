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
        'location_description',
        'localidad_id',
        'privacy_level',
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

    public function localidad()
    {
        return $this->belongsTo(Localidad::class);
    }

    public function photos()
    {
        return $this->hasMany(IncidentPhoto::class)->orderBy('order');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at')->where('status', 'verified');
    }

    public function getReporterNameAttribute()
    {
        if ($this->privacy_level === 'IDENTIFIED') {
            return $this->user->name;
        }
        return 'Anónimo';
    }

    public function assignLocalidad()
    {
        // Use PostGIS to find the locality containing this incident's location
        // We assume 'location' is already a valid geometry in the DB
        $localidad = \Illuminate\Support\Facades\DB::selectOne("
            SELECT id FROM localidades 
            WHERE ST_Intersects(
                geom, 
                (SELECT location FROM incidents WHERE id = ?)
            )
            LIMIT 1
        ", [$this->id]);

        if ($localidad) {
            $this->localidad_id = $localidad->id;
            $this->save();
        }
    }
}
