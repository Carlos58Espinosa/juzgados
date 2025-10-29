<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasosUsuarios extends Model
{
    protected $table = 'casos_usuarios';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
