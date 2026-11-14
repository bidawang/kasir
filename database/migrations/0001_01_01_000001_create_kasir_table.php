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
        Schema::create('kasir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('user')->cascadeOnDelete();
            $table->string('nama_warung');
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 30)->nullable();
            $table->unsignedBigInteger('modal')->default(0);

            // Pengaturan Bank Utama Toko / Cabang (Default: BRI, agnostik untuk bank lain)
            $table->string('bank_utama', 50)->default('BRI');
            $table->string('nomor_rekening_utama', 50)->nullable();
            $table->string('atas_nama_utama', 100)->nullable();

            $table->timestamps();
        });

        // Tabel pivot user yang ditugaskan ke cabang / warung
        Schema::create('kasir_user', function (Blueprint $table) {
            $table->foreignId('id_kasir')->constrained('kasir')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('user')->cascadeOnDelete();
            $table->primary(['id_kasir', 'id_user']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasir_user');
        Schema::dropIfExists('kasir');
    }
};
