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
        Schema::create('kategori_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique(); // tf_sesama_kartu, tf_sesama_app, tf_bank_lain, tarik_utama_kartu, tarik_utama_app, tarik_bank_lain, ewallet_topup, ewallet_tarik, briva_va, ppob_tagihan
            $table->string('nama_kategori', 100);
            $table->string('jenis_transaksi', 30)->default('transfer'); // transfer, tarik_tunai, ewallet, ppob
            $table->string('metode_transaksi', 30)->default('umum');    // kartu, tanpa_kartu, aplikasi, qris, umum
            $table->enum('jenis_aliran', ['masuk', 'keluar'])->default('masuk'); // masuk = uang fisik kasir bertambah, keluar = uang fisik kasir keluar
            $table->enum('jenis_mutasi_akun', ['masuk', 'keluar'])->nullable();
            $table->string('deskripsi', 255)->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('badge_color', 50)->default('blue'); // blue, emerald, rose, indigo, amber, purple
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_transaksi');
    }
};
