<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logo extends Model
{
    protected $table = 'logos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
