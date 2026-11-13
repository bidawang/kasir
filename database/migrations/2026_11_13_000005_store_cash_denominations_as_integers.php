<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['detail_kas', 'detail_transaksi'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedInteger('pecahan_numeric')->nullable();
            });

            $castType = DB::connection()->getDriverName() === 'mysql' ? 'UNSIGNED' : 'INTEGER';
            DB::table($tableName)->update([
                'pecahan_numeric' => DB::raw("CAST(CAST(pecahan AS CHAR) AS {$castType})"),
            ]);

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('pecahan');
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('pecahan_numeric', 'pecahan');
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedInteger('pecahan')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        foreach (['detail_kas', 'detail_transaksi'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('pecahan', 6)->change();
            });
        }
    }
};
