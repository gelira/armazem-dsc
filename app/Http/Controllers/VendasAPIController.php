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

    public function balanco($inicio, $fim)
    {
        $dados = [
            'inicio' => $inicio, 
            'fim' => $fim
        ];
        $regras = [
            'inicio' => 'date_format:d-m-Y', 
            'fim' => 'date_format:d-m-Y'
        ];

        $validator = Validator::make($dados, $regras);
        if ($validator->fails())
        {
            return response()->json(['message' => 'Erro de sintaxe. Data inválida'], 400);
        }

        $data_inicio = \DateTime::createFromFormat('d-m-Y', $inicio);
        $data_fim = \DateTime::createFromFormat('d-m-Y', $fim);

        if ($data_inicio >= $data_fim)
        {
            return response()->json(['message' => 'Data final anterior ou igual à data inicial'], 400);
        }

        $retorno = [
            'data_inicio' => $data_inicio->format('d/m/Y'),
            'data_fim' => $data_fim->format('d/m/Y'),
            'vendas_totais' => 0,
            'lucro' => 0,
            'produtos' => []
        ];

        $produtos_vendidos = Produto::with('vendas')
            ->join('vendas', 'produtos.id', '=', 'vendas.produto_id')
            ->where('vendas.data', '>=', $data_inicio)
            ->where('vendas.data', '<=', $data_fim)
            ->select('produtos.*')
            ->groupBy('produtos.id')
            ->get();

        foreach ($produtos_vendidos as $produto)
        {
            $quantidade = 0;
            $lucro = 0;
            $preco_unitario = 0;
            $preco_total = 0;

            foreach ($produto->vendas as $venda) 
            {
                $quantidade += $venda->quantidade;
                $lucro += ($venda->preco_total - $venda->quantidade * $venda->preco_unitario);
                $preco_unitario += $venda->preco_unitario * $venda->quantidade;
                $preco_total += $venda->preco_total;
            }

            $p = [
                'produto_id' => $produto->id,
                'media_preco_unitario' => ($preco_unitario / $quantidade),
                'media_preco_total' => ($preco_total / $quantidade)
            ];

            $retorno['vendas_totais'] += $quantidade;
            $retorno['lucro'] += $lucro;
            $retorno['produtos'][] = $p;
        }

        return response()->json($retorno, 200);
    }
}
