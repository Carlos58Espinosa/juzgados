<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatoCaso extends Model
{
    protected $table = 'formatos_casos';
    protected $guarded = ['id'];
    public $timestamps = false;
}
