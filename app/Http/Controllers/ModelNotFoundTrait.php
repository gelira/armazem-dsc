<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ModelNotFoundTrait
{
    private function getModel($id)
    {
        try
        {
            return $this->searchModelDB($id);
        }

        catch (ModelNotFoundException $e)
        {
            $e->setModel($this->modelName);
            throw $e;
        }
    }
}