<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_transaksi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kas_warung')->constrained('kas')->onDelete('cascade');
            $table->unsignedBigInteger('total');
            $table->string('metode_pembayaran');
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->enum('jenis', ['keluar', 'masuk']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};