<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/divisiones/listar', [DivisionController::class, 'listarDivisiones']);
Route::get('/divisiones/listar-subdivisiones-por-id', [DivisionController::class, 'listarSubdivisionesPorId']);
Route::post('/divisiones/crear-division', [DivisionController::class, 'crearDivisones']);
Route::put('/divisiones/actualizar-division-por-id', [DivisionController::class, 'actualizarDivisiones']);
Route::delete('/divisiones/delete-division', [DivisionController::class, 'eliminarDivision']);

Route::get('/test-database', function () {
    try {
        DB::connection()->getPdo();
        print_r("Connected successfully to: " . DB::connection()->getDatabaseName());
    } catch (\Exception $e) {
        die("Could not connect to the database.  Please check your configuration. Error:" . $e );
    }
});