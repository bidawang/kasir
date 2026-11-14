<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_kasir_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kasir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('user');
            $table->string('nama_warung');
            $table->unsignedBigInteger('modal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasir');
    }
};