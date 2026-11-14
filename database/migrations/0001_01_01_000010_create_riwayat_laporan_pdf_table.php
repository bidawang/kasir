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
        Schema::create('riwayat_laporan_pdf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('kasir')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('user')->cascadeOnDelete();
            $table->string('nomor_laporan', 50)->unique();
            $table->string('judul', 255);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('id_kategori')->nullable()->constrained('kategori_transaksi')->nullOnDelete();
            $table->string('kategori_label', 100)->nullable();
            $table->decimal('total_masuk', 15, 2)->default(0);
            $table->decimal('total_keluar', 15, 2)->default(0);
            $table->decimal('total_keuntungan', 15, 2)->default(0);
            $table->decimal('total_admin_toko', 15, 2)->default(0);
            $table->decimal('total_admin_bank', 15, 2)->default(0);
            $table->integer('total_transaksi')->default(0);
            $table->text('catatan')->nullable();
            $table->json('filter_snapshot')->nullable();
            $table->timestamps();

            $table->index(['id_warung', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_laporan_pdf');
    }
};
