<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Insumo;
use Illuminate\Support\Facades\Validator;

class InsumosAPIController extends Controller
{
    use ModelNotFoundTrait;

    private $modelName = 'Insumo';

    private function searchModelDB($id)
    {
        if ($id == null)
        {
            return new Insumo();
        }
        return Insumo::where('id', $id)->firstOrFail();
    }

    public function salvar(Request $rq, $id = null)
    {
        $insumo = $this->getModel($id);
        $regras = [
            'descricao' => 'required|max:50',
            'materia_prima' => 'required|boolean',
            'estoque' => 'nullable|numeric',
            'fornecedor_id' => 'required|integer|exists:fornecedors,id',
            'unidade_id' => 'required|integer|exists:unidades,id'
        ];

        Validator::make($rq->all(), $regras, [
            'fornecedor_id.exists' => 'Fornecedor not found',
            'unidade_id.exists' => 'Unidade not found'
        ])->validate();

        $dados = $rq->only(array_keys($regras));
        $insumo->fill($dados)->save();

        return response()->json(['id' => $insumo->id], 200);
    }

    public function listar()
    {
        return response()->json(
            Insumo::select('id', 'descricao', 'materia_prima', 'estoque')->get(), 
        200);
    }

    public function consultar($id)
    {
        return response()->json(
            $this->getModel($id)
                ->with(['fornecedor', 'unidade'])
                ->get(), 
        200);
    }
}
