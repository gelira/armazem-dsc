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
            'compras_totais' => 0,
            'insumos' => []
        ];

        $insumos_comprados = Insumo::join('compras', 'insumos.id', '=', 'compras.insumo_id')
            ->where('compras.data_compra', '>=', $data_inicio->format('Y-m-d'))
            ->where('compras.data_compra', '<=', $data_fim->format('Y-m-d'))
            ->select('insumos.*')
            ->groupBy('insumos.id')
            ->get();

        foreach ($insumos_comprados as $insumo)
        {
            $quantidade = 0;
            $preco_unitario = 0;
            $preco_total = 0;

            $compras = $insumo->compras()
                ->where('compras.data_compra', '>=', $data_inicio->format('Y-m-d'))
                ->where('compras.data_compra', '<=', $data_fim->format('Y-m-d'))
                ->get();

            foreach ($compras as $compra) 
            {
                $quantidade += $compra->quantidade;
                $preco_unitario += $compra->valor_unitario * $compra->quantidade;
                $preco_total += $compra->valor_total;
            }

            $p = [
                'insumo_id' => $insumo->id,
                'media_preco_unitario' => ($preco_unitario / $quantidade),
                'media_preco_total' => ($preco_total / $quantidade)
            ];

            $retorno['compras_totais'] += $quantidade;
            $retorno['insumos'][] = $p;
        }

        return response()->json($retorno, 200);
    }
}
