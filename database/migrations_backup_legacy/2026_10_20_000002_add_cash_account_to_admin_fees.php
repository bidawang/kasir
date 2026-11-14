<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_admin', function (Blueprint $table) {
            $table->foreignId('id_kas')->nullable()
                ->after('id_warung')
                ->constrained('kas')
                ->nullOnDelete();
            $table->index(['id_warung', 'jenis', 'id_kas', 'range_awal', 'range_akhir'], 'biaya_admin_kas_range_index');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_admin', function (Blueprint $table) {
            $table->dropIndex('biaya_admin_kas_range_index');
            $table->dropConstrainedForeignId('id_kas');
        });
    }
};
