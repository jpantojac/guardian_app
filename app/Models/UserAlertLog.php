<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAlertLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'localidad_id'];
}
