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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // Misal: BRI, BCA, MANDIRI, BNI, BSI, DANA, OVO, GOPAY, SHOPEEPAY
            $table->string('nama', 100);          // Misal: Bank Rakyat Indonesia, DANA
            $table->enum('tipe', ['bank', 'ewallet'])->default('bank');
            $table->string('icon', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
