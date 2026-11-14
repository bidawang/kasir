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
        Schema::create('biaya_admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('kasir')->cascadeOnDelete();
            $table->foreignId('id_kategori')->nullable()->constrained('kategori_transaksi')->cascadeOnDelete();
            $table->foreignId('id_kas')->nullable()->constrained('kas')->nullOnDelete();
            $table->string('jenis', 30)->nullable(); // Tarif barang/layanan legacy: fisik, virtual, bank

            $table->unsignedBigInteger('range_awal');
            $table->unsignedBigInteger('range_akhir');
            $table->unsignedBigInteger('biaya_admin');                  // Tarif Admin Toko / Jasa yang ditagih ke nasabah (keuntungan)
            $table->unsignedBigInteger('biaya_admin_bank')->default(0);     // Modal potongan resmi dari Bank / Server
            $table->string('keterangan', 255)->nullable();

            $table->timestamps();

            $table->index(['id_warung', 'id_kategori', 'range_awal', 'range_akhir'], 'biaya_admin_kategori_range_index');
            $table->index(['id_warung', 'jenis', 'range_awal', 'range_akhir'], 'biaya_admin_jenis_range_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_admin');
    }
};
