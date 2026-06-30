<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formasi_asn', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jabatan_id')
                ->constrained('jabatan')
                ->onDelete('cascade');
            $table->integer('tahun');
            $table->integer('jpt')->default(0);
            $table->integer('adm_pengawas')->default(0);
            $table->integer('mutasi')->default(0);
            $table->integer('cpns')->default(0);
            $table->integer('pppk')->default(0);
            $table->timestamps();
            $table->unique(['tahun', 'jabatan_id']);
            $table->index('tahun');
            $table->index('jabatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formasi_asn');
    }
};