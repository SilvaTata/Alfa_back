<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistSolicitar extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitar_id',
        'hora_inicio',
        'data_inicio',
        'hora_final',
        'data_final',
        'obs_users'
    ];

    public function solicitar() 
    {
        return $this->belongsTo(solicitar::class, 'solicitar_id');
    }
}
