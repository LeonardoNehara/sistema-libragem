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

    public function composicoesComoCavalo()
    {
        return $this->hasMany(Composicao::class, 'cavalo_id');
    }

    public function composicoesComoCarreta1()
    {
        return $this->hasMany(Composicao::class, 'carreta_1_id');
    }

    public function composicoesComoCarreta2()
    {
        return $this->hasMany(Composicao::class, 'carreta_2_id');
    }

    public function pneusColetados()
    {
        return $this->hasMany(ChecklistPneu::class);
    }
}