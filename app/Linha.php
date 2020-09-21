<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Linha extends Model
{
    protected $table = 'linhas';

    protected $fillable = [
        'nome', 'frequencia', 'cod_empresa', 'horario_inicio','horario_fim'
    ];
}
