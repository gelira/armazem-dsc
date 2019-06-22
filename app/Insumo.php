<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $fillable = [
        'unidade_id', 'fornecedor_id', 'descricao', 'materia_prima', 'estoque'
    ];

    protected $attributes = [
        'estoque' => 0
    ];

    public function filtrarEstoque($estoque)
    {
        if ($estoque == null)
        {
            $estoque = $this->attributes['estoque'];
        }
        return $estoque;
    }

    public function unidade()
    {
        return $this->belongsTo('App\Unidade');
    }

    public function fornecedor()
    {
        return $this->belongsTo('App\Fornecedor');
    }
}
