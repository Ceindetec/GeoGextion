<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupertransporteTraportador extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supertransporte_transportador', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('transportador_id')->unsigned();
            $table->integer('supertransporte_id')->unsigned();
            $table->foreign('transportador_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supertransporte_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('supertransporte_transportador');
    }
}
