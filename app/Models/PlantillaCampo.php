<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaCampo extends Model
{
    protected $table = 'plantillas_campos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
