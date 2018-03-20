<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoPosicionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_posicions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('latitud');
            $table->string('longitud');
            $table->string('direccion')->nullable();
            $table->dateTime('fecha');
            $table->string('identificacion',10)->index();
            $table->foreign('identificacion')->references('identificacion')->on('asesores')->onDelete('cascade');
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
        Schema::dropIfExists('geo_posicions');
    }
}
