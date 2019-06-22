<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $fillable = [
        'unidade_id', 'fornecedor_id', 'descricao', 'materia_prima', 'estoque'
    ];
}
