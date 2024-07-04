<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasosValores extends Model
{
    protected $table = 'casos_valores';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
