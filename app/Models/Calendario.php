<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    protected $table = 'calendario';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
