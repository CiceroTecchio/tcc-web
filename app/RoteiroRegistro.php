<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoteiroRegistro extends Model
{
    protected $table = 'roteiros_registro';

    protected $fillable = [
        'cod_linha', 'cod_veiculo'
    ];
}
