<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Linha extends Model
{
    protected $table = 'linhas';

    protected $fillable = [
        'nome', 'frequencia', 'cod_empresa', 'horario_inicio','horario_fim', 'fg_domingo', 'fg_segunda', 'fg_terca', 'fg_quarta', 'fg_quinta', 'fg_sexta', 'fg_sabado'
    ];
}
