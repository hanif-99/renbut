<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 255);
            $table->foreignId('unit_organisasi_id')
                ->constrained('unit_organisasi')
                ->onDelete('cascade');
            $table->foreignId('jenis_jabatan_id')
                ->nullable()
                ->constrained('jenis_jabatan');
            $table->foreignId('jenjang_id')
                ->nullable()
                ->constrained('jenjang');
            $table->string('kj', 100)->nullable();
            $table->integer('b')->default(0);  // Beban/Existing
            $table->integer('k')->default(0);  // Keadaan/Filled
            $table->timestamps();
            $table->index('kode');
            $table->index('unit_organisasi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};