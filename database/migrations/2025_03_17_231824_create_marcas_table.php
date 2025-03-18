<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarcasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('marca', 50)->unique();
            $table->timestamps();
        });

        \DB::table('marcas')->insert([
            ['marca' => 'Toyota'],
            ['marca' => 'Honda'],
            ['marca' => 'Ford'],
            ['marca' => 'Chevrolet'],
            ['marca' => 'Volkswagen'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marcas');
    }
}
