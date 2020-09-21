<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ponto extends Model
{
    protected $table = 'pontos_parada';

    protected $fillable = [
        'latitude', 'longitude'
    ];
}
