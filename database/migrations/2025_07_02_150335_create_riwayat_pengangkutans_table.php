<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_pengangkutan', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->float('berat');
            $table->float('tinggi');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->timestamp('waktu_angkut');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengangkutans');
    }
};
