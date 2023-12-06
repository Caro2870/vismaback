<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisionsTable extends Migration
{
    public function up()
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 45)->unique();
            $table->unsignedBigInteger('division_superior_id')->nullable();
            $table->integer('colaboradores');
            $table->integer('nivel');
            $table->string('embajador_nombre')->nullable();
            $table->timestamps();

            $table->foreign('division_superior_id')->references('id')->on('divisions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('divisions');
    }
}
