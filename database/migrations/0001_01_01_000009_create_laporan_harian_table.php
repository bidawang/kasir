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
        Schema::create('laporan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('kasir')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('user')->cascadeOnDelete();
            $table->foreignId('id_pelapor')->constrained('user')->cascadeOnDelete();
            $table->date('tanggal');
            $table->json('rincian_fisik')->nullable(); // Rincian pecahan fisik kasir saat closing
            $table->unsignedBigInteger('saldo_fisik');
            $table->unsignedBigInteger('saldo_sistem');
            $table->bigInteger('selisih');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['id_warung', 'id_user', 'tanggal']);
            $table->index(['id_warung', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_harian');
    }
};
