<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kasir_user')) {
            Schema::create('kasir_user', function (Blueprint $table) {
                $table->foreignId('id_kasir')->constrained('kasir')->cascadeOnDelete();
                $table->foreignId('id_user')->constrained('user')->cascadeOnDelete();
                $table->timestamps();
                $table->primary(['id_kasir', 'id_user']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kasir_user');
    }
};
