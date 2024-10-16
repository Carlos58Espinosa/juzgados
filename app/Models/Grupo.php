<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function campos(){
        return $this->hasMany('App\Models\GrupoCampo', 'grupoId', 'id');
    }

}
