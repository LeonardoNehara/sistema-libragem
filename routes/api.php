<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\ChecklistCalibragemController;

Route::apiResource('empresas', EmpresaController::class);
Route::apiResource('veiculos', VeiculoController::class);
Route::apiResource('checklists-calibragem', ChecklistCalibragemController::class);