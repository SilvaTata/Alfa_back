<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Solicitar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'veiculo_id',
        'prev_hora_inicio',
        'prev_data_inicio',
        'prev_hora_final',
        'prev_data_final',
        'motivo',
        'situacao',
        'motivo_recusa'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function historico()
    {
        return $this->hasOne(HistSolicitar::class, 'solicitar_id');
    }
}
