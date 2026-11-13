<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kasir_user')) {
            Schema::create('kasir_user', function (Blueprint $table) {
                $table->foreignId('id_kasir')->constrained('kasir')->cascadeOnDelete();
                $table->foreignId('id_user')->constrained('user')->cascadeOnDelete();
                $table->primary(['id_kasir', 'id_user']);
                $table->timestamps();
            });
        }

        if (Schema::hasColumn('user', 'id_warung')) {
            $assignments = DB::table('user')
                ->whereNotNull('id_warung')
                ->get(['id', 'id_warung']);

            foreach ($assignments as $assignment) {
                DB::table('kasir_user')->insertOrIgnore([
                    'id_kasir' => $assignment->id_warung,
                    'id_user' => $assignment->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::table('user', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_warung');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('user', 'id_warung')) {
            Schema::table('user', function (Blueprint $table) {
                $table->foreignId('id_warung')->nullable()
                    ->after('role')
                    ->constrained('kasir')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('kasir_user')) {
            foreach (DB::table('kasir_user')->orderBy('id_user')->get() as $assignment) {
                DB::table('user')
                    ->where('id', $assignment->id_user)
                    ->whereNull('id_warung')
                    ->update(['id_warung' => $assignment->id_kasir]);
            }

            Schema::drop('kasir_user');
        }
    }
};
