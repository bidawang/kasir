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
        Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('kasir')->cascadeOnDelete();
            $table->string('jenis_kas')->default('Kas Utama'); // Nama kas/rekening
            $table->string('status')->default('cash');          // 'cash', 'bank', 'ewallet'
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('atas_nama', 100)->nullable();
            $table->bigInteger('saldo')->default(0);
            $table->boolean('is_primary')->default(false);      // Penanda kas tunai utama / rekening operasional utama
            $table->timestamps();
        });

        Schema::create('detail_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kas_warung')->constrained('kas')->cascadeOnDelete();
            $table->unsignedInteger('pecahan');                // 100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000
            $table->integer('jumlah')->default(0);             // Jumlah lembar / keping
            $table->timestamps();

            $table->unique(['id_kas_warung', 'pecahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kas');
        Schema::dropIfExists('kas');
    }
};
