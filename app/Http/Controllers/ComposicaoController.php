<?php

namespace App\Http\Controllers;

use App\Models\Composicao;
use App\Models\Veiculo;
use Illuminate\Http\Request;

class ComposicaoController extends Controller
{
    public function index()
    {
        return response()->json(
            Composicao::with('empresa', 'cavalo', 'carreta1', 'carreta2')->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'cavalo_id' => 'required|exists:veiculos,id',
            'carreta_1_id' => 'nullable|exists:veiculos,id',
            'carreta_2_id' => 'nullable|exists:veiculos,id',
            'data_composicao' => 'nullable|date',
        ]);

        $cavalo = Veiculo::findOrFail($dados['cavalo_id']);

        if ($cavalo->tipo !== 'cavalo') {
            return response()->json([
                'message' => 'O veículo selecionado como cavalo precisa ser do tipo cavalo.'
            ], 422);
        }

        if (!empty($dados['carreta_1_id'])) {
            $carreta1 = Veiculo::findOrFail($dados['carreta_1_id']);

            if ($carreta1->tipo !== 'carreta') {
                return response()->json([
                    'message' => 'O veículo selecionado como carreta 1 precisa ser do tipo carreta.'
                ], 422);
            }
        }

        if (!empty($dados['carreta_2_id'])) {
            $carreta2 = Veiculo::findOrFail($dados['carreta_2_id']);

            if ($carreta2->tipo !== 'carreta') {
                return response()->json([
                    'message' => 'O veículo selecionado como carreta 2 precisa ser do tipo carreta.'
                ], 422);
            }
        }

        $composicao = Composicao::create($dados);

        return response()->json([
            'message' => 'Composição criada com sucesso',
            'data' => $composicao->load('empresa', 'cavalo', 'carreta1', 'carreta2')
        ], 201);
    }

    public function show($id)
    {
        $composicao = Composicao::with([
            'empresa',
            'cavalo',
            'carreta1',
            'carreta2',
            'checklists.pneus.veiculo'
        ])->findOrFail($id);

        return response()->json($composicao);
    }

    public function update(Request $request, $id)
    {
        $composicao = Composicao::findOrFail($id);

        $dados = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'cavalo_id' => 'required|exists:veiculos,id',
            'carreta_1_id' => 'nullable|exists:veiculos,id',
            'carreta_2_id' => 'nullable|exists:veiculos,id',
            'data_composicao' => 'nullable|date',
        ]);

        $composicao->update($dados);

        return response()->json([
            'message' => 'Composição atualizada com sucesso',
            'data' => $composicao->load('empresa', 'cavalo', 'carreta1', 'carreta2')
        ]);
    }

    public function destroy($id)
    {
        $composicao = Composicao::findOrFail($id);
        $composicao->delete();

        return response()->json([
            'message' => 'Composição excluída com sucesso'
        ]);
    }
}