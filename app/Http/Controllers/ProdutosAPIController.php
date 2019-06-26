<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produto;
use Illuminate\Support\Facades\Validator;
use App\InsumoProduto;

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
            'insumos.*.insumo_id' => 'required|integer|exists:insumos,id',
            'insumos.*.quantidade' => 'required|numeric|min:0',
        ];

        if ($id == null)
        {
            $regras['insumos'] = 'required|array';
        }
        else 
        {
            $regras['insumos'] = 'nullable|array';
            $regras['estoque'] = 'required|integer|min:0';
            $lista_dados[] = 'estoque';
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
}
