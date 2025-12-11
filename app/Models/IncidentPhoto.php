<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IncidentPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'photo_path',
        'order',
    ];

    protected $appends = ['url'];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->photo_path);
    }
}
