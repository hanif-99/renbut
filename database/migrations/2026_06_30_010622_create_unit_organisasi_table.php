<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_organisasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 255);
            $table->foreignId('perangkat_daerah_id')
                ->constrained('perangkat_daerah')
                ->onDelete('cascade');
            $table->string('unor_atasan', 255)->nullable();
            $table->timestamps();
            $table->index('kode');
            $table->index('perangkat_daerah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_organisasi');
    }
};