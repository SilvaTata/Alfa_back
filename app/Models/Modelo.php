<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = [
        'modelo'
    ];

    public function veiculos() 
    {
        return $this->hasMany(Veiculo::class);
    }
}
