<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    protected $table = 'localidades';

    protected $fillable = [
        'nombre',
        'codigo',
        // 'geom' is handled via DB statements or accessor if needed
    ];

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
