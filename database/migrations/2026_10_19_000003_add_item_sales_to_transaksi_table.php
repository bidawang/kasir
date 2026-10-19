<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('kas', 'jenis_kas')) {
            Schema::table('kas', function (Blueprint $table) {
                $table->string('jenis_kas')->nullable();
            });
        }

        if (!Schema::hasColumn('kas', 'status')) {
            Schema::table('kas', function (Blueprint $table) {
                $table->string('status')->nullable();
            });
        }

        if (!Schema::hasColumn('transaksi', 'kategori')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->string('kategori')->nullable();
            });
        }

        if (!Schema::hasColumn('transaksi', 'biaya_admin')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->unsignedBigInteger('biaya_admin')->default(0);
            });
        }

        if (!Schema::hasColumn('transaksi', 'id_item_jualan')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->foreignId('id_item_jualan')->nullable()
                    ->constrained('item_jualan')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('transaksi', 'nama_item')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->string('nama_item')->nullable();
            });
        }

        if (!Schema::hasColumn('transaksi', 'harga_item')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->unsignedBigInteger('harga_item')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi', 'id_item_jualan')) {
                $table->dropForeign(['id_item_jualan']);
                $table->dropColumn('id_item_jualan');
            }

            foreach (['nama_item', 'harga_item'] as $column) {
                if (Schema::hasColumn('transaksi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
