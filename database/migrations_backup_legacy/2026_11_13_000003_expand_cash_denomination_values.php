<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DENOMINATIONS = ['100', '200', '500', '1000', '2000', '5000', '10000', '20000', '50000', '100000'];

    public function up(): void
    {
        foreach (['detail_kas', 'detail_transaksi'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('pecahan', self::DENOMINATIONS)->change();
            });
        }
    }

    public function down(): void
    {
        foreach (['detail_kas', 'detail_transaksi'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('pecahan', self::DENOMINATIONS)->change();
            });
        }
    }
};
