<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_detail_kas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kas_warung')->constrained('kas')->onDelete('cascade');
            $table->enum('pecahan', ['1000', '2000', '5000', '10000', '20000', '50000', '100000']);
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_kas');
    }
};