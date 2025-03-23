<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'chassi',
        'status_veiculo',
        'qr_code',
        'ano',
        'cor',
        'capacidade',
        'obs_veiculo',
        'km_revisao',
        'marca_id',
        'modelo_id'
    ];

    public function marca() 
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo() 
    {
        return $this->belongsTo(Modelo::class);
    }

    public function solicitars()
    {
        return $this->hasMany(Solicitar::class, 'veiculo_id');
    }
}
