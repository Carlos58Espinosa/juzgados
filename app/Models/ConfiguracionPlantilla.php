<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionPlantilla extends Model
{
    protected $table = 'configuracion_plantillas';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function plantilla(){
        return $this->hasOne('App\Models\Plantilla', 'id', 'plantillaId');
    }

    public function plantilla_campos(){
        return $this->hasMany('App\Models\PlantillaCampo', 'plantillaId', 'plantillaId');
    }

    public function configuracion(){
        return $this->hasOne('App\Models\Configuracion', 'id', 'configuracionId');
    }
}
