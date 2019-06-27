<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $fillable = [
        'descricao', 'faixa_etaria_min', 'faixa_etaria_max', 'num_patente', 'estoque'
    ];

    protected $attributes = [
        'estoque' => 0
    ];

    public function insumos()
    {
        return $this->belongsToMany('App\Insumo')
            ->using('App\InsumoProduto')
            ->withPivot('quantidade');
    }

    public function producaos()
    {
        return $this->hasMany('App\Producao');
    }

    public function vendas()
    {
        return $this->hasMany('App\Venda');
    }
}
