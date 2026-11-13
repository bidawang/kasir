<?php

namespace Tests\Feature;

use App\Models\Kas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CabangTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_branch_with_all_supported_cash_denominations(): void
    {
        $this->assertSame('integer', Schema::getColumnType('detail_kas', 'pecahan'));
        $this->assertSame('integer', Schema::getColumnType('detail_transaksi', 'pecahan'));

        $now = now();
        $adminId = DB::table('user')->insertGetId([
            'nama' => 'Admin Uji',
            'nomor_hp' => '08123456789',
            'username' => 'adminuji',
            'email' => 'adminuji@example.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $admin = User::findOrFail($adminId);

        $this->actingAs($admin)
            ->post(route('cabang.store'), [
                'nama_warung' => 'Cabang Baru',
            ])
            ->assertRedirect(route('cabang.index'));

        $warung = \App\Models\Kasir::where('nama_warung', 'Cabang Baru')->firstOrFail();
        $cash = Kas::where('id_warung', $warung->id)
            ->where('status', 'cash')
            ->firstOrFail();
        $this->assertSame(
            [100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000],
            DB::table('detail_kas')
                ->where('id_kas_warung', $cash->id)
                ->pluck('pecahan')
                ->map(fn ($pecahan) => (int) $pecahan)
                ->sort()
                ->values()
                ->all()
        );
        $this->assertSame(
            0,
            DB::table('detail_kas')
                ->where('id_kas_warung', $cash->id)
                ->sum('jumlah')
        );
        $this->assertDatabaseMissing('transaksi', [
            'id_kas_warung' => $cash->id,
        ]);
    }
}
