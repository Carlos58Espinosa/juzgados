<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasoPlantillaLog extends Model
{
    protected $table = 'casos_plantillas_logs';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
