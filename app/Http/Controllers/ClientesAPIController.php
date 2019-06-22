<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cliente;

class ClientesAPIController extends Controller
{
    use ModelNotFoundTrait;

    private $modelName = 'Cliente';

    private function searchModelDB($id)
    {
        if ($id == null)
        {
            return new Cliente();
        }
        return Cliente::where('id', $id)->firstOrFail();
    }

    public function salvar(Request $rq, $id = null)
    {
        $rq->validate([
            'cnpj' => 'required|max:14',
            'fantasia' => 'required|max:50',
            'razao' => 'required|max:50'
        ]);

        $cliente = $this->getModel($id);
        $cliente->fill($rq->only('cnpj', 'fantasia', 'razao'))->save();

        return response()->json(['id' => $cliente->id], 200);
    }

    public function listar()
    {
        return response()->json(Cliente::all(), 200);
    }

    public function consultar($id)
    {
        return response()->json($this->getModel($id), 200);
    }
}
