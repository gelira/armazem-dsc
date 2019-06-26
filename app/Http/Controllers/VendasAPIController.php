<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venda;
use Illuminate\Support\Facades\Validator;
use App\Produto;

class VendasAPIController extends Controller
{
    public function realizarVenda(Request $rq)
    {
        $regras = [
            'produto_id' => 'required|integer|exists:produtos,id',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'quantidade' => 'required|integer|min:1',
            'preco_unitario' => 'required|numeric|min:0',
            'base_lucro' => 'required|numeric|min:1',
            'nf' => 'required|string|max:100',
        ];

        Validator::make($rq->all(), $regras, [
            'produto_id.exists' => 'Produto not found',
            'cliente_id.exists' => 'Cliente not found'
        ]);

        $produto = Produto::find($rq->produto_id);
        if ($produto->estoque < $rq->quantidade)
        {
            return response()->json(['message' => 'Estoque insuficiente'], 401);
        }

        $produto->estoque -= $rq->quantidade;
        $produto->save();

        $dados = $rq->only(array_keys($regras));
        $venda = new Venda($dados);
        $venda->preco_total = $venda->quantidade * $venda->preco_unitario * $venda->base_lucro;
        $venda->data = now();
        $venda->save();

        return response()->json([
            'id' => $venda->id,
            'preco_total' => $venda->preco_total
        ], 200);
    }
}
