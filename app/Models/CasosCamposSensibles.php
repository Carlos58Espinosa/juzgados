<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasosCamposSensibles extends Model
{
    protected $table = 'casos_campos_sensibles';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
