<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermisos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id('id_permiso');
            $table->string('tipo_usuario', 20)->nullable()->default(null);
            $table->string('cod_sis', 50)->nullable()->default(null);
            $table->dateTime('fecha_solicitud')->nullable()->default(null);
            $table->dateTime('fecha_fin_solicitud')->nullable()->default(null);
            $table->dateTime('fecha_permiso')->nullable()->default(null);
            $table->text('motivo')->nullable()->default(null);
            $table->string('cod_comite', 50)->nullable()->default(null);
            $table->string('estado', 50)->nullable()->default(null);
            $table->string('codsis_sustituto', 50)->nullable()->default(null);
            $table->tinyInteger('comprobante_entregado')->nullable()->default(null);
            $table->dateTime('fecha_comprobante_entregado')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permisos');
    }
}
