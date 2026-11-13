<?php

namespace Tests\Feature;

use App\Models\DetailKas;
use App\Models\Kas;
use App\Models\Kasir;
use App\Models\LaporanHarian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LaporanHarianTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_can_submit_only_one_cash_report_per_day(): void
    {
        [$kasir, $warung] = $this->createUserAndWarung('kasir');
        Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 125000,
        ]);
        $this->actingAs($kasir)->withSession(['id_warung' => $warung->id]);

        $this->get(route('laporan-harian.index'))
            ->assertOk()
            ->assertSee('Saldo kas sistem saat ini')
            ->assertSee('Jumlah lembar')
            ->assertSee('name="jumlah[100000]"', false)
            ->assertDontSee('name="saldo_fisik"', false);
        $this->post(route('laporan-harian.store'), [
            'jumlah' => $this->cashCounts([
                100000 => 1,
                20000 => 1,
                5000 => 1,
            ]),
        ])->assertRedirect(route('laporan-harian.index'));

        $this->assertDatabaseHas('laporan_harian', [
            'id_warung' => $warung->id,
            'id_user' => $kasir->id,
            'saldo_sistem' => 125000,
            'saldo_fisik' => 125000,
            'selisih' => 0,
        ]);
        $this->assertSame(
            $this->cashCounts([100000 => 1, 20000 => 1, 5000 => 1]),
            LaporanHarian::firstOrFail()->rincian_fisik
        );

        $this->post(route('laporan-harian.store'), [
            'jumlah' => $this->cashCounts([100000 => 1, 20000 => 1]),
        ])->assertSessionHasErrors('jumlah');

        $this->assertSame(1, LaporanHarian::count());
    }

    public function test_owner_can_correct_an_employee_report_multiple_times(): void
    {
        [$owner, $warung] = $this->createUserAndWarung('owner');
        $kasir = $this->createUser('kasir');
        DB::table('kasir_user')->insert([
            'id_kasir' => $warung->id,
            'id_user' => $kasir->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 100000,
        ]);
        $this->actingAs($owner)->withSession(['id_warung' => $warung->id]);

        $this->get(route('laporan-harian.index'))
            ->assertOk()
            ->assertSee('Input atau koreksi laporan')
            ->assertSee($kasir->nama);
        $this->post(route('laporan-harian.store'), [
            'id_user' => $kasir->id,
            'jumlah' => $this->cashCounts([
                50000 => 1,
                20000 => 2,
                5000 => 1,
            ]),
            'catatan' => 'Koreksi pertama',
        ])->assertRedirect(route('laporan-harian.index'));

        $this->post(route('laporan-harian.store'), [
            'id_user' => $kasir->id,
            'jumlah' => $this->cashCounts([100000 => 1]),
            'catatan' => 'Sudah dihitung ulang',
        ])->assertRedirect(route('laporan-harian.index'));

        $this->assertSame(1, LaporanHarian::count());
        $this->assertDatabaseHas('laporan_harian', [
            'id_user' => $kasir->id,
            'id_pelapor' => $owner->id,
            'saldo_fisik' => 100000,
            'saldo_sistem' => 100000,
            'selisih' => 0,
            'catatan' => 'Sudah dihitung ulang',
        ]);
        $this->assertSame($this->cashCounts([100000 => 1]), LaporanHarian::firstOrFail()->rincian_fisik);
    }

    private function createUserAndWarung(string $role): array
    {
        $user = $this->createUser($role);
        $warung = Kasir::create([
            'id_user' => $user->id,
            'nama_warung' => 'Warung Uji',
            'modal' => 0,
        ]);

        return [$user, $warung];
    }

    private function cashCounts(array $overrides = []): array
    {
        return array_replace(array_fill_keys(DetailKas::DENOMINATIONS, 0), $overrides);
    }

    private function createUser(string $role): User
    {
        $sequence = DB::table('user')->count() + 1;
        $userId = DB::table('user')->insertGetId([
            'nama' => ucfirst($role) . ' Uji',
            'nomor_hp' => '08123456789',
            'username' => $role . $sequence,
            'email' => $role . $sequence . '@example.test',
            'password' => bcrypt('password'),
            'role' => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($userId);
    }
}
