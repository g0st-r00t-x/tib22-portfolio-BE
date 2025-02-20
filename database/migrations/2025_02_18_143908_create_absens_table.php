<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id('id_absensi');
            $table->foreignId('id_dosen')->constrained('dosen')->references('id_dosen')->onDelete('cascade');
            $table->foreignId('id_jadwal')->constrained('jadwal')->references('id_jadwal')->onDelete('cascade');
            $table->foreignId('id_mahasiswa')->constrained('mahasiswa')->references('id_mahasiswa')->onDelete('cascade');
            $table->dateTime('waktu_absen');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi');
    }
};
