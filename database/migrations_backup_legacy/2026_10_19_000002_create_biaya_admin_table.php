<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('kasir')->cascadeOnDelete();
            $table->unsignedBigInteger('range_awal');
            $table->unsignedBigInteger('range_akhir');
            $table->unsignedBigInteger('biaya_admin');
            $table->timestamps();

            $table->index(['id_warung', 'range_awal', 'range_akhir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_admin');
    }
};
