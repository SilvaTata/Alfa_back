<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique(); // Nome do cargo (gerente, usuário)
            $table->timestamps();
        });

        // Adicionando os cargos iniciais
        \DB::table('cargos')->insert([
            ['nome' => 'gerente'],
            ['nome' => 'usuario'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('cargos');
    }
};
