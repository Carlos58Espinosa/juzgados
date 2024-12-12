<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasoLogo extends Model
{
    protected $table = 'casos_logos';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function logo(){
        return $this->hasOne('App\Models\Logo', 'id', 'logoId');
    }
}
