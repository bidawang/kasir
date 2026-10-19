<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_jualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('kasir')->cascadeOnDelete();
            $table->string('nama_item');
            $table->unsignedBigInteger('harga_jual');
            $table->timestamps();

            $table->index(['id_warung', 'nama_item']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_jualan');
    }
};
