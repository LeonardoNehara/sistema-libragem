<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Composicao extends Model
{
    protected $table = 'composicoes';

    protected $fillable = [
        'empresa_id',
        'cavalo_id',
        'carreta_1_id',
        'carreta_2_id',
        'data_composicao',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cavalo()
    {
        return $this->belongsTo(Veiculo::class, 'cavalo_id');
    }

    public function carreta1()
    {
        return $this->belongsTo(Veiculo::class, 'carreta_1_id');
    }

    public function carreta2()
    {
        return $this->belongsTo(Veiculo::class, 'carreta_2_id');
    }

    public function checklists()
    {
        return $this->hasMany(ChecklistCalibragem::class);
    }
}