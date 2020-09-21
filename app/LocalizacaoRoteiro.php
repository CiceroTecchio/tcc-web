<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalizacaoRoteiro extends Model
{
    protected $table = 'localizacao_roteiro';

    protected $fillable = [
        'cod_roteiro_registro', 'latitude', 'longitude'
    ];
}
