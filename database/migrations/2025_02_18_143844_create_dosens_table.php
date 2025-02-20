<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id('id_dosen');
            $table->string('nama');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dosen');
    }
};
