<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('veiculo_id')->constrained('veiculos');
            $table->time('prev_hora_inicio');
            $table->date('prev_data_inicio');
            $table->time('prev_hora_final');
            $table->date('prev_data_final');
            $table->text('motivo');
            $table->enum('situacao', ['pendente', 'aceita', 'recusada', 'concluída'])->default('pendente');
            $table->text('motivo_recusa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitars');
    }
}
