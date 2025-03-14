<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->check("nome IN ('adm', 'user')");
            $table->timestamps();
        });

        // Adicionando os cargos iniciais
        \DB::table('cargos')->insert([
            ['nome' => 'adm'],
            ['nome' => 'user'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('cargos');
    }
};
