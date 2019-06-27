<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = [
        'insumo_id', 'quantidade', 'valor_unitario', 'icms', 'frete', 
        'ipi', 'nf', 'data_compra', 'data_entrega'
    ];
}
