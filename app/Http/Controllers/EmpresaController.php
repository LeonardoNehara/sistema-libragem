<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        return response()->json(
            Empresa::with('veiculos', 'composicoes')->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $empresa = Empresa::create($dados);

        return response()->json([
            'message' => 'Empresa cadastrada com sucesso',
            'data' => $empresa
        ], 201);
    }

    public function show($id)
    {
        $empresa = Empresa::with([
            'veiculos',
            'composicoes.cavalo',
            'composicoes.carreta1',
            'composicoes.carreta2',
            'composicoes.checklists.pneus'
        ])->findOrFail($id);

        return response()->json($empresa);
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);

        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $empresa->update($dados);

        return response()->json([
            'message' => 'Empresa atualizada com sucesso',
            'data' => $empresa
        ]);
    }

    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();

        return response()->json([
            'message' => 'Empresa excluída com sucesso'
        ]);
    }
}