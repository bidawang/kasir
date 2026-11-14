<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_jualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('kasir')->cascadeOnDelete();
            $table->string('nama_item');
            $table->string('jenis', 30)->default('fisik'); // 'fisik', 'virtual', 'bank'
            $table->unsignedBigInteger('harga_jual')->default(0);
            $table->unsignedBigInteger('harga_beli')->default(0);
            $table->integer('jumlah')->default(0);          // Stok item fisik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_jualan');
    }
};
