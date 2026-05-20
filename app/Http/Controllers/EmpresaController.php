<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        return response()->json(Empresa::with('veiculos')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $empresa = Empresa::create($request->all());

        return response()->json([
            'message' => 'Empresa cadastrada com sucesso',
            'data' => $empresa
        ], 201);
    }

    public function show($id)
    {
        $empresa = Empresa::with('veiculos.checklists.pneus')->findOrFail($id);

        return response()->json($empresa);
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $empresa->update($request->all());

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