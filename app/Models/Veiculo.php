<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    protected $fillable = [
        'empresa_id',
        'placa',
        'tipo',
        'quantidade_eixos',
        'quantidade_pneus',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function checklists()
    {
        return $this->hasMany(ChecklistCalibragem::class);
    }
}