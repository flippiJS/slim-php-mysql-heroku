<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'usuarios';
    public $incrementing = true;

    protected $fillable = [
        'usuario', 'clave'
    ];
}