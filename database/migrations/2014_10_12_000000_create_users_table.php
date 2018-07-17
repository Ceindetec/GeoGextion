<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('identificacion')->nullable()->index();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('telefono')->nullable();
            $table->enum('estado', ['A', 'I'])->defalut('A');
            $table->integer('empresa_id')->unsigned()->nullable();
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
