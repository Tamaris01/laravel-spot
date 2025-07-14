<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aktivitas_penggunaparkir', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_pengguna');
            $table->string('aktivitas'); // contoh: login, scan_qr, parkir_masuk
            $table->text('keterangan')->nullable(); // opsional
            $table->dateTime('waktu_aktivitas')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna_parkir')
                ->onDelete('cascade');

            $table->index('id_pengguna');
            $table->index('waktu_aktivitas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivitas_penggunaparkir');
    }
};
