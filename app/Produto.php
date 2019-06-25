<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $fillable = [
        'descricao', 'faixa_etaria_min', 'faixa_etaria_max', 'num_patente'
    ];

    protected $attributes = [
        'estoque' => 0
    ];
}
