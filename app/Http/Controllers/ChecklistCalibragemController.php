<?php

namespace App\Http\Controllers;

use App\Models\ChecklistCalibragem;
use App\Models\ChecklistPneu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistCalibragemController extends Controller
{
    public function index()
    {
        return response()->json(
            ChecklistCalibragem::with('veiculo.empresa', 'pneus')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'veiculo_id' => 'required|exists:veiculos,id',
            'data_coleta' => 'required|date',
            'tecnico_nome' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'status' => 'nullable|in:rascunho,finalizado',

            'pneus' => 'required|array',
            'pneus.*.posicao' => 'required|integer',
            'pneus.*.libragem' => 'required|integer',
            'pneus.*.status' => 'nullable|in:calibrado,baixo,alto,critico',
        ]);

        DB::beginTransaction();

        try {
            $checklist = ChecklistCalibragem::create([
                'veiculo_id' => $request->veiculo_id,
                'data_coleta' => $request->data_coleta,
                'tecnico_nome' => $request->tecnico_nome,
                'observacoes' => $request->observacoes,
                'status' => $request->status ?? 'rascunho',
            ]);

            foreach ($request->pneus as $pneu) {
                ChecklistPneu::create([
                    'checklist_id' => $checklist->id,
                    'posicao' => $pneu['posicao'],
                    'libragem' => $pneu['libragem'],
                    'status' => $pneu['status'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Checklist de calibragem cadastrado com sucesso',
                'data' => $checklist->load('pneus')
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
        $checklist = ChecklistCalibragem::with('veiculo.empresa', 'pneus')->findOrFail($id);

        return response()->json($checklist);
    }

    public function update(Request $request, $id)
    {
        $checklist = ChecklistCalibragem::findOrFail($id);

        $request->validate([
            'data_coleta' => 'required|date',
            'tecnico_nome' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'status' => 'nullable|in:rascunho,finalizado',

            'pneus' => 'required|array',
            'pneus.*.posicao' => 'required|integer',
            'pneus.*.libragem' => 'required|integer',
            'pneus.*.status' => 'nullable|in:calibrado,baixo,alto,critico',
        ]);

        DB::beginTransaction();

        try {
            $checklist->update([
                'data_coleta' => $request->data_coleta,
                'tecnico_nome' => $request->tecnico_nome,
                'observacoes' => $request->observacoes,
                'status' => $request->status ?? $checklist->status,
            ]);

            $checklist->pneus()->delete();

            foreach ($request->pneus as $pneu) {
                ChecklistPneu::create([
                    'checklist_id' => $checklist->id,
                    'posicao' => $pneu['posicao'],
                    'libragem' => $pneu['libragem'],
                    'status' => $pneu['status'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Checklist atualizado com sucesso',
                'data' => $checklist->load('pneus')
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