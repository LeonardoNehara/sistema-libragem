<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    public function index()
    {
        return response()->json(
            Veiculo::with('empresa')->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'placa' => 'required|string|max:20',
            'tipo' => 'required|in:cavalo,carreta',
            'quantidade_eixos' => 'nullable|integer',
            'quantidade_pneus' => 'nullable|integer',
        ]);

        $veiculo = Veiculo::create($dados);

        return response()->json([
            'message' => 'Veículo cadastrado com sucesso',
            'data' => $veiculo
        ], 201);
    }

    public function show($id)
    {
        $veiculo = Veiculo::with([
            'empresa',
            'pneusColetados.checklist'
        ])->findOrFail($id);

        return response()->json($veiculo);
    }

    public function update(Request $request, $id)
    {
        $veiculo = Veiculo::findOrFail($id);

        $dados = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'placa' => 'required|string|max:20',
            'tipo' => 'required|in:cavalo,carreta',
            'quantidade_eixos' => 'nullable|integer',
            'quantidade_pneus' => 'nullable|integer',
        ]);

        $veiculo->update($dados);

        return response()->json([
            'message' => 'Veículo atualizado com sucesso',
            'data' => $veiculo
        ]);
    }

    public function destroy($id)
    {
        $veiculo = Veiculo::findOrFail($id);
        $veiculo->delete();

        return response()->json([
            'message' => 'Veículo excluído com sucesso'
        ]);
    }
}