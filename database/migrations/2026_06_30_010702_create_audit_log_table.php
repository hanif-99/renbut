<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 100);
            $table->bigInteger('record_id');
            $table->string('action', 20);  // INSERT, UPDATE, DELETE
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index('created_at');
            $table->index('table_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};