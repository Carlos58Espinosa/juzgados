<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasosPlantillas extends Model
{
    protected $table = 'casos_plantillas';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function plantilla_campos(){
        return $this->hasMany('App\Models\PlantillaCampo', 'plantillaId', 'plantillaId');
    }

    public function plantilla(){
        return $this->hasOne('App\Models\Plantilla', 'id', 'plantillaId');
    }
}
