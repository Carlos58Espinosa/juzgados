<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarioUsuario extends Model
{
    protected $table = 'calendario_usuarios';
    protected $guarded = ['id'];
    public $timestamps = false;

}
