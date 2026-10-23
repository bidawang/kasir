<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('user', 'role')) {
            Schema::table('user', function (Blueprint $table) {
                $table->string('role', 20)->default('kasir');
            });
        }

        if (!Schema::hasColumn('user', 'status')) {
            Schema::table('user', function (Blueprint $table) {
                $table->string('status', 20)->default('aktif');
            });
        }

        if (!Schema::hasColumn('user', 'email_verified_at')) {
            Schema::table('user', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable();
            });
        }

        Schema::table('user', function (Blueprint $table) {
            $table->string('nomor_hp')->nullable()->change();
            $table->string('username')->nullable()->change();
        });

        DB::table('kas')->whereNull('jenis_kas')->update(['jenis_kas' => 'Kas Utama']);
        DB::table('kas')->whereNull('status')->update(['status' => 'cash']);
        Schema::table('kas', function (Blueprint $table) {
            $table->string('jenis_kas')->default('Kas Utama')->change();
            $table->string('status')->default('cash')->change();
        });

        if (!Schema::hasTable('kategori_transaksi')) {
            Schema::create('kategori_transaksi', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kategori');
                $table->enum('jenis', ['masuk', 'keluar']);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('transaksi', 'id_kategori')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->foreignId('id_kategori')->nullable()
                    ->constrained('kategori_transaksi')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasTable('transfer_kas')) {
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
    }

    public function down(): void
    {
        if (Schema::hasColumn('transaksi', 'id_kategori')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_kategori');
            });
        }

        Schema::dropIfExists('transfer_kas');
        Schema::dropIfExists('kategori_transaksi');

        foreach (['role', 'status'] as $column) {
            if (Schema::hasColumn('user', $column)) {
                Schema::table('user', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasColumn('user', 'email_verified_at')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropColumn('email_verified_at');
            });
        }
    }
};
