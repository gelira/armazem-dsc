<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producao extends Model
{
    protected $fillable = ['data', 'lote', 'quantidade', 'perecivel', 'expira_em'];
}
