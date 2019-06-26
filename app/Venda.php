<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $fillable = [
        'cliente_id', 'produto_id', 'quantidade', 'preco_unitario', 'base_lucro', 'nf'
    ];
}
