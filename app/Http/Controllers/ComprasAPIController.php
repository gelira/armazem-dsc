<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Compra;
use App\Insumo;

class ComprasAPIController extends Controller
{
    public function realizarCompra(Request $rq)
    {
        $regras = [
            'insumo_id' => 'required|integer|exists:insumos,id',
            'quantidade' => 'required|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
            'icms' => 'required|numeric|min:0',
            'frete' => 'required|numeric|min:0',
            'ipi' => 'required|numeric|min:0',
            'nf' => 'required|string|max:100',
            'data_compra' => 'required|date_format:d/m/Y',
            'data_entrada' => 'required|date_format:d/m/Y'
        ];
        Validator::make($rq->all(), $regras, ['exists' => 'Insumo not found'])->validate();

        $insumo = Insumo::find($rq->insumo_id);
        $insumo->estoque += $rq->quantidade;
        $insumo->save();

        $v = $rq->quantidade * $rq->valor_unitario * (1 + $rq->icms) * (1 + $rq->ipi) + $rq->frete;
        $dados = $rq->only(array_keys($regras));
        $compra = new Compra($dados);
        $compra->valor_total = $v;
        $compra->save();

        return response()->json(['id' => $compra->id], 200);
    }
}
