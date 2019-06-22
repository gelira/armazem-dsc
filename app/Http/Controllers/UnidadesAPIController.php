<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unidade;

class UnidadesAPIController extends Controller
{
    use ModelNotFoundTrait;

    private $modelName = 'Unidade';

    private function searchModelDB($id)
    {
        if ($id == null)
        {
            return new Unidade();
        }
        return Unidade::where('id', $id)->firstOrFail();
    }

    public function salvar(Request $rq, $id = null)
    {
        $rq->validate([
            'descricao' => 'required|max:50',
            'grama' => 'required|numeric'
        ]);

        $unidade = $this->getModel($id);
        $unidade->fill($rq->only('descricao', 'grama'))->save();

        return response()->json(['id' => $unidade->id], 200);
    }

    public function listar()
    {
        return response()->json(Unidade::all(), 200);
    }
}
