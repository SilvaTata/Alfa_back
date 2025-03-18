<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistSolicitarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hist_solicitars', function (Blueprint $table) {
            $table->id();
            $table->foreignid('solicitacao_id')->constrained('solicitars')->onDelete('cascade')->unique();
            $table->time('hora_inicio');
            $table->date('data_inicio');
            $table->time('hora_final');
            $table->date('data_final');
            $table->text('obs_users')->nullable();
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
        Schema::dropIfExists('hist_solicitars');
    }
}
