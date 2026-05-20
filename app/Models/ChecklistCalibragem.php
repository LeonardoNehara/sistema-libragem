<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistCalibragem extends Model
{
    protected $table = 'checklists_calibragem';

    protected $fillable = [
        'veiculo_id',
        'data_coleta',
        'tecnico_nome',
        'observacoes',
        'status',
    ];

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function pneus()
    {
        return $this->hasMany(ChecklistPneu::class, 'checklist_id');
    }
}