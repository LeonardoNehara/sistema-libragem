<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = [
        'nome',
        'cnpj',
        'telefone',
        'email',
    ];

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class);
    }
}