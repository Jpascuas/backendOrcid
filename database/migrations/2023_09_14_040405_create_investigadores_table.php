<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //crea la estructura de la tabla para crear los campos al hacer el php artisan migrate
        Schema::create('investigadores', function (Blueprint $table) {
            $table->id();
            $table->string('orcid')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->json('palabras_clave')->nullable();
            $table->string('correo_principal')->nullable();
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
        Schema::dropIfExists('investigadores');
    }
}
