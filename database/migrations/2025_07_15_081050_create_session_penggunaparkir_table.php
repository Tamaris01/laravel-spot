<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('session_penggunaparkir', function (Blueprint $table) {
            $table->id();
            $table->string('id_pengguna');
            $table->uuid('session_id')->unique();
            $table->dateTime('session_start');
            $table->dateTime('session_end')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna_parkir')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_penggunaparkir');
    }
};
