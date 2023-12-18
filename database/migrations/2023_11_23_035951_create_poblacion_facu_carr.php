<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoblacionFacuCarr extends Migration
{

    public function up()
    {
        Schema::create('poblacion_facu_carr', function (Blueprint $table) {
            $table->char('codsis', 25);
            $table->unsignedBigInteger('cod_facultad');
            $table->unsignedBigInteger('cod_carrera');
        });
    }

    public function down()
    {
        Schema::dropIfExists('poblacion_facu_carr');
    }
}
