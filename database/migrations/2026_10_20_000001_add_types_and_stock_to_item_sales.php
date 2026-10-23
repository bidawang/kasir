<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_jualan', function (Blueprint $table) {
            $table->enum('jenis', ['fisik', 'virtual', 'bank'])->default('bank');
            $table->unsignedInteger('jumlah')->nullable();
        });

        Schema::table('biaya_admin', function (Blueprint $table) {
            $table->enum('jenis', ['fisik', 'virtual', 'bank'])->default('virtual');
            $table->index(['id_warung', 'jenis', 'range_awal', 'range_akhir'], 'biaya_admin_warung_jenis_range_index');
        });

        if (!Schema::hasColumn('transaksi', 'id_kas_sumber')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->foreignId('id_kas_sumber')->nullable()
                    ->after('id_kas_warung')
                    ->constrained('kas')
                    ->nullOnDelete();
            });
        }

        foreach (['detail_kas', 'detail_transaksi'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('pecahan', ['100', '200', '500', '1000', '2000', '5000', '10000', '20000', '50000', '100000'])->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transaksi', 'id_kas_sumber')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_kas_sumber');
            });
        }

        Schema::table('biaya_admin', function (Blueprint $table) {
            $table->dropIndex('biaya_admin_warung_jenis_range_index');
            $table->dropColumn('jenis');
        });

        Schema::table('item_jualan', function (Blueprint $table) {
            $table->dropColumn(['jenis', 'jumlah']);
        });

        foreach (['detail_kas', 'detail_transaksi'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('pecahan', ['1000', '2000', '5000', '10000', '20000', '50000', '100000'])->change();
            });
        }
    }
};
