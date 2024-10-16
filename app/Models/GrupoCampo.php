<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoCampo extends Model
{
    protected $table = 'grupos_campos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
