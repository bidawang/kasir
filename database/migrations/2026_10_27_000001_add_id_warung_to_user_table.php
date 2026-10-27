<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('user', 'id_warung')) {
            Schema::table('user', function (Blueprint $table) {
                $table->foreignId('id_warung')->nullable()
                    ->after('role')
                    ->constrained('kasir')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user', 'id_warung')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_warung');
            });
        }
    }
};
