<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_kas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('kasir')->onDelete('cascade');
            $table->unsignedBigInteger('saldo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};