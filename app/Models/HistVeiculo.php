<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistVeiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'veiculo_id',
        'km_inicio',
        'km_final'
    ];

    public function veiculo() 
    {
        return $this->belongsTo(Veiculo::class);
    }
}
