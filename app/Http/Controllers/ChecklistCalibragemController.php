<?php

namespace App\Http\Controllers;

use App\Models\ChecklistCalibragem;
use App\Models\ChecklistPneu;
use App\Models\Composicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistCalibragemController extends Controller
{
    public function index()
    {
        return response()->json(
            ChecklistCalibragem::with([
                'composicao.empresa',
                'composicao.cavalo',
                'composicao.carreta1',
                'composicao.carreta2',
                'pneus.veiculo'
            ])->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'composicao_id' => 'required|exists:composicoes,id',
            'data_coleta' => 'required|date',
            'tecnico_nome' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'status' => 'nullable|in:rascunho,finalizado',

            'pneus' => 'required|array|min:1',
            'pneus.*.veiculo_id' => 'required|exists:veiculos,id',
            'pneus.*.posicao' => 'required|integer',
            'pneus.*.libragem' => 'required|integer',
            'pneus.*.status' => 'nullable|in:calibrado,baixo,alto,critico',
        ]);

        $composicao = Composicao::findOrFail($dados['composicao_id']);

        $veiculosPermitidos = array_filter([
            $composicao->cavalo_id,
            $composicao->carreta_1_id,
            $composicao->carreta_2_id,
        ]);

        foreach ($dados['pneus'] as $pneu) {
            if (!in_array($pneu['veiculo_id'], $veiculosPermitidos)) {
                return response()->json([
                    'message' => 'Existe pneu informado para um veículo que não faz parte da composição.'
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $checklist = ChecklistCalibragem::create([
                'composicao_id' => $dados['composicao_id'],
                'data_coleta' => $dados['data_coleta'],
                'tecnico_nome' => $dados['tecnico_nome'] ?? null,
                'observacoes' => $dados['observacoes'] ?? null,
                'status' => $dados['status'] ?? 'rascunho',
            ]);

            foreach ($dados['pneus'] as $pneu) {
                ChecklistPneu::create([
                    'checklist_id' => $checklist->id,
                    'veiculo_id' => $pneu['veiculo_id'],
                    'posicao' => $pneu['posicao'],
                    'libragem' => $pneu['libragem'],
                    'status' => $pneu['status'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Checklist de calibragem cadastrado com sucesso',
                'data' => $checklist->load('composicao.cavalo', 'composicao.carreta1', 'composicao.carreta2', 'pneus.veiculo')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao cadastrar checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $checklist = ChecklistCalibragem::with([
            'composicao.empresa',
            'composicao.cavalo',
            'composicao.carreta1',
            'composicao.carreta2',
            'pneus.veiculo'
        ])->findOrFail($id);

        return response()->json($checklist);
    }

    public function update(Request $request, $id)
    {
        $checklist = ChecklistCalibragem::findOrFail($id);

        $dados = $request->validate([
            'data_coleta' => 'required|date',
            'tecnico_nome' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'status' => 'nullable|in:rascunho,finalizado',

            'pneus' => 'required|array|min:1',
            'pneus.*.veiculo_id' => 'required|exists:veiculos,id',
            'pneus.*.posicao' => 'required|integer',
            'pneus.*.libragem' => 'required|integer',
            'pneus.*.status' => 'nullable|in:calibrado,baixo,alto,critico',
        ]);

        DB::beginTransaction();

        try {
            $checklist->update([
                'data_coleta' => $dados['data_coleta'],
                'tecnico_nome' => $dados['tecnico_nome'] ?? null,
                'observacoes' => $dados['observacoes'] ?? null,
                'status' => $dados['status'] ?? $checklist->status,
            ]);

            $checklist->pneus()->delete();

            foreach ($dados['pneus'] as $pneu) {
                ChecklistPneu::create([
                    'checklist_id' => $checklist->id,
                    'veiculo_id' => $pneu['veiculo_id'],
                    'posicao' => $pneu['posicao'],
                    'libragem' => $pneu['libragem'],
                    'status' => $pneu['status'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Checklist atualizado com sucesso',
                'data' => $checklist->load('pneus.veiculo')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao atualizar checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $checklist = ChecklistCalibragem::findOrFail($id);
        $checklist->delete();

        return response()->json([
            'message' => 'Checklist excluído com sucesso'
        ]);
    }
}