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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->nullable()->unique();
            $table->foreignId('id_user')->nullable()->constrained('user')->nullOnDelete();
            $table->foreignId('id_kas_warung')->constrained('kas')->cascadeOnDelete();
            $table->foreignId('id_kas_sumber')->nullable()->constrained('kas')->nullOnDelete();
            $table->foreignId('id_kategori')->nullable()->constrained('kategori_transaksi')->nullOnDelete();
            $table->foreignId('id_item_jualan')->nullable()->constrained('item_jualan')->nullOnDelete();

            // Kategori teks tambahan untuk backward-compatibility & query reporting
            $table->string('kategori', 50)->default('transaksi_customer');
            $table->string('sub_kategori', 50)->nullable();

            // Metode Eksekusi: 'kartu', 'tanpa_kartu', 'aplikasi', 'qris', 'cash', 'transfer'
            $table->string('metode_transaksi', 30)->nullable();
            $table->string('metode_pembayaran', 50)->default('cash');

            // Data Tujuan / Nasabah
            $table->string('bank_tujuan', 100)->nullable();      // Bank / E-Wallet tujuan (misal: BRI, BCA, DANA, PLN)
            $table->string('nomor_tujuan', 100)->nullable();     // No Rekening / No HP / ID Pelanggan / No VA
            $table->string('nama_tujuan', 150)->nullable();      // Nama Pemilik / Penerima

            // Data Finansial & Fee
            $table->unsignedBigInteger('nominal')->default(0);            // Nominal transaksi pokok (misal: Rp 500.000)
            $table->unsignedBigInteger('biaya_admin')->default(0);        // Biaya Admin Toko (Jasa agen yang ditagih ke nasabah)
            $table->unsignedBigInteger('biaya_admin_bank')->default(0);   // Modal Potongan Resmi dari Bank/Server
            $table->bigInteger('keuntungan')->default(0);                 // Laba Bersih Toko = biaya_admin - biaya_admin_bank
            $table->unsignedBigInteger('kas_masuk')->default(0);
            $table->unsignedBigInteger('kas_keluar')->default(0);
            $table->unsignedBigInteger('total');                          // Total Nilai Kas Fisik (Masuk / Keluar)
            $table->boolean('is_profit')->default(false);

            // Arah Arus Kas Fisik: 'masuk' (uang fisik masuk kasir) / 'keluar' (uang fisik keluar ke nasabah)
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->string('status', 20)->default('sukses');              // 'sukses', 'pending', 'batal'

            // Data Penjualan Item (jika ada)
            $table->string('nama_item')->nullable();
            $table->unsignedBigInteger('harga_item')->nullable();

            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();

            $table->index(['id_kas_warung', 'tanggal']);
            $table->index(['id_kategori', 'tanggal']);
            $table->index(['jenis', 'tanggal']);
        });

        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi_kas')->constrained('transaksi')->cascadeOnDelete();
            $table->unsignedInteger('pecahan');
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
        Schema::dropIfExists('transaksi');
    }
};
