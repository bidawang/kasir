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
        Schema::create('transfer_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kas_asal')->constrained('kas')->cascadeOnDelete();
            $table->foreignId('id_kas_tujuan')->constrained('kas')->cascadeOnDelete();
            $table->unsignedBigInteger('jumlah');
            $table->unsignedBigInteger('biaya_transfer')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_kas');
    }
};
