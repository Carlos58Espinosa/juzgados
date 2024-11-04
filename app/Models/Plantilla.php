<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
     protected $guarded = ['id', 'created_at', 'updated_at'];

     public function usuario(){
          return $this->hasOne('App\User', 'id', 'usuarioId');
     }
}
