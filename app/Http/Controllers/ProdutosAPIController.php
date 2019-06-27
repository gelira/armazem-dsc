<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produto;
use Illuminate\Support\Facades\Validator;
use App\InsumoProduto;
use App\Producao;

class ProdutosAPIController extends Controller
{
    use ModelNotFoundTrait;

    private $modelName = 'Produto';

    private function searchModelDB($id)
    {
        if ($id == null)
        {
            return new Produto();
        }
        return Produto::where('id', $id)->firstOrFail();
    }

    private function salvarInsumoProduto($produto_id, $insumo_id, $quantidade)
    {
        $insprd = InsumoProduto::where('produto_id', $produto_id)
            ->where('insumo_id', $insumo_id)
            ->first(); 
        if ($insprd == null)
        {
            $insprd = new InsumoProduto(['produto_id' => $produto_id, 'insumo_id' => $insumo_id]);
        }
        $insprd->quantidade = $quantidade;
        $insprd->save();
    }

    public function salvar(Request $rq, $id = null)
    {
        $produto = $this->getModel($id);

        $lista_dados = ['descricao', 'faixa_etaria_min', 'faixa_etaria_max', 'num_patente'];
        $regras = [
            'descricao' => 'required|string|max:50',
            'faixa_etaria_min' => 'required|integer|min:0',
            'faixa_etaria_max' => 'required|integer|min:0',
            'num_patente' => 'required|string|max:10',
            'insumos' => 'required|array',
            'insumos.*.insumo_id' => 'required|integer|exists:insumos,id',
            'insumos.*.quantidade' => 'required|numeric|min:0',
        ];

        if ($id != null)
        {
            $regras['estoque'] = 'required|integer|min:0';
            $lista_dados[] = 'estoque';
            $produto->insumos()->detach();
        }

        Validator::make($rq->all(), $regras, ['exists' => 'Insumo not found'])->validate();

        $produto->fill($rq->only($lista_dados))->save();
        if ($rq->filled('insumos'))
        {
            foreach ($rq->insumos as $ins)
            {
                $this->salvarInsumoProduto($produto->id, $ins['insumo_id'], $ins['quantidade']);
            }
        }

        return response()->json(['id' => $produto->id], 200);
    }

    public function listar()
    {
        return response()->json(Produto::all(), 200);
    }

    public function consultar($id)
    {
        $produto = $this->getModel($id)
            ->with('insumos')
            ->where('id', $id)
            ->first();
        return response()->json($produto, 200);
    }

    public function produzir(Request $rq)
    {
        Validator::make($rq->all(), [
            'produto_id' => 'required|integer|exists:produtos,id',
            'lote' => 'required|integer',
            'quantidade' => 'required|integer|min:1',
            'perecivel' => 'required|boolean',
            'expira_em' => 'required|date_format:d/m/Y'
        ], [
            'exists' => 'Produto not found',
            'date_format' => 'Data inválida'
        ])->validate();

        $produto = $this->getModel($rq->produto_id);
        $qtd = $rq->quantidade;
        foreach ($produto->insumos as $insumo)
        {
            if ($qtd * $insumo->pivot->quantidade > $insumo->estoque)
            {
                return response()->json(['message' => 'Insumos insuficientes'], 401);
            }
        }

        $producao = (new Producao())
            ->fill($rq->only('lote', 'quantidade', 'perecivel', 'expira_em'))
            ->fill(['data' => now()]);
        $produto->producaos()->save($producao);

        foreach ($produto->insumos as $insumo)
        {
            $insumo->estoque -= $qtd * $insumo->pivot->quantidade;
            $insumo->save();
        }

        $produto->estoque += $qtd;
        $produto->save();

        return response()->json(['id' => $producao->id], 200);
    }
}
