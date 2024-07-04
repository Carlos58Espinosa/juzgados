<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasoPlantillaCampo extends Model
{
    protected $table = 'casos_plantillas_campos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
