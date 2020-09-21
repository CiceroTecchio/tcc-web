<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarcaVeiculo extends Model
{
    protected $table = 'marcas_veiculo';

    protected $fillable = [
        'descricao_marca'
    ];
}
