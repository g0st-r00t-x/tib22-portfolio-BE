<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pelajaran', function (Blueprint $table) {
            $table->id('id_pelajaran');
            $table->string('nama_pelajaran');
            $table->string('kode_pelajaran')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pelajaran');
    }
};
