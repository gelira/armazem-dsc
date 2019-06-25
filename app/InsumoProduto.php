<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InsumoProduto extends Pivot
{
    protected $table = 'insumo_produto';

    protected $fillable = ['produto_id', 'insumo_id', 'quantidade'];
}
