<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fornecedor;

class FornecedorsAPIController extends Controller
{
    use ModelNotFoundTrait;

    private $modelName = 'Fornecedor';

    private function searchModelDB($id)
    {
        if ($id == null)
        {
            return new Fornecedor();
        }
        return Fornecedor::where('id', $id)->firstOrFail();
    }

    public function salvar(Request $rq, $id = null)
    {
        $rq->validate([
            'cnpj' => 'required|max:14',
            'fantasia' => 'required|max:50',
            'razao' => 'required|max:50'
        ]);

        $fornecedor = $this->getModel($id);
        $fornecedor->fill($rq->only('cnpj', 'fantasia', 'razao'))->save();

        return response()->json(['id' => $fornecedor->id], 200);
    }

    public function listar()
    {
        return response()->json(Fornecedor::all(), 200);
    }

    public function consultar($id)
    {
        return response()->json($this->getModel($id), 200);
    }
}
