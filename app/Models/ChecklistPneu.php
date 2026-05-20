<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistPneu extends Model
{
    protected $table = 'checklist_pneus';

    protected $fillable = [
        'checklist_id',
        'posicao',
        'libragem',
        'status',
    ];

    public function checklist()
    {
        return $this->belongsTo(ChecklistCalibragem::class, 'checklist_id');
    }
}