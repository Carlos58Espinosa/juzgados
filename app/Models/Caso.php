<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caso extends Model
{
    protected $table = 'casos';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function configuracion(){
        return $this->hasOne('App\Models\Configuracion', 'id', 'configuracionId');
    }

    public function etapa_plantilla(){
        return $this->hasOne('App\Models\Plantilla', 'id', 'etapaPlantillaId');
    }

    public function plantillas(){
        return $this->hasMany('App\Models\CasosPlantillas', 'casoId', 'id');
    }

    public function valores(){
        return $this->hasMany('App\Models\CasosValores', 'casoId', 'id')->orderBy('orden', 'asc');
    }

    public function campos(){
        return $this->hasMany('App\Models\CasoPlantillaCampo', 'casoId', 'id');
    }
}
