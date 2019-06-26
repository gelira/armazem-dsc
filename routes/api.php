<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('clientes')->group(function () {
    Route::post('salvar/{id?}', 'ClientesAPIController@salvar');
    Route::get('listar', 'ClientesAPIController@listar');
    Route::get('consultar/{id}', 'ClientesAPIController@consultar');
});

Route::prefix('fornecedors')->group(function () {
    Route::post('salvar/{id?}', 'FornecedorsAPIController@salvar');
    Route::get('listar', 'FornecedorsAPIController@listar');
    Route::get('consultar/{id}', 'FornecedorsAPIController@consultar');
});

Route::prefix('unidades')->group(function () {
    Route::post('salvar/{id?}', 'UnidadesAPIController@salvar');
    Route::get('listar', 'UnidadesAPIController@listar');
});

Route::prefix('insumos')->group(function () {
    Route::post('salvar/{id?}', 'InsumosAPIController@salvar');
    Route::get('listar', 'InsumosAPIController@listar');
    Route::get('consultar/{id}', 'InsumosAPIController@consultar');
});

Route::prefix('produtos')->group(function () {
    Route::post('salvar/{id?}', 'ProdutosAPIController@salvar');
    Route::get('listar', 'ProdutosAPIController@listar');
    Route::get('consultar/{id}', 'ProdutosAPIController@consultar');
    Route::put('produzir', 'ProdutosAPIController@produzir');
});

Route::prefix('vendas')->group(function () {
    Route::put('realizar', 'VendasAPIController@realizarVenda');
});