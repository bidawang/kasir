<?php

namespace Tests\Feature;

use App\Models\Kas;
use App\Models\Kasir;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManajemenKasirTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_update_and_delete_cashier_accounts_for_owned_branches(): void
    {
        [$owner, $warung] = $this->createOwnerAndBranch();
        $this->actingAs($owner);

        $this->post(route('manajemen-kasir.store'), [
            'nama' => 'Kasir Utama',
            'email' => 'kasir@example.test',
            'nomor_hp' => '08123456789',
            'id_kasir' => $warung->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'aktif',
        ])->assertRedirect(route('manajemen-kasir.index'));

        $kasir = User::where('email', 'kasir@example.test')->firstOrFail();
        $this->assertSame('kasir', $kasir->role);
        $this->assertTrue($kasir->warungs->contains('id', $warung->id));
        $this->assertTrue(Hash::check('password123', $kasir->password));

        $this->post(route('logout'));
        $this->post(route('login'), [
            'email' => 'kasir@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));
        $this->assertSame($warung->id, session('id_warung'));

        $this->actingAs($owner);
        $this->get(route('manajemen-kasir.edit', $kasir))->assertOk()->assertSee('Edit Akun Kasir');
        $this->put(route('manajemen-kasir.update', $kasir), [
            'nama' => 'Kasir Cabang',
            'email' => 'kasir@example.test',
            'nomor_hp' => '08111111111',
            'id_kasir' => $warung->id,
            'password' => '',
            'password_confirmation' => '',
            'status' => 'nonaktif',
        ])->assertRedirect(route('manajemen-kasir.index'));

        $this->assertDatabaseHas('user', [
            'id' => $kasir->id,
            'nama' => 'Kasir Cabang',
            'status' => 'nonaktif',
        ]);

        $this->delete(route('manajemen-kasir.destroy', $kasir))
            ->assertRedirect(route('manajemen-kasir.index'));
        $this->assertDatabaseMissing('user', ['id' => $kasir->id]);
    }

    public function test_cashier_management_is_scoped_to_the_owner_and_assigned_branch(): void
    {
        [$owner, $warung] = $this->createOwnerAndBranch();
        [$otherOwner, $otherWarung] = $this->createOwnerAndBranch('other-owner@example.test');
        $otherKasir = User::create([
            'nama' => 'Kasir Orang Lain',
            'email' => 'other-kasir@example.test',
            'role' => 'kasir',
            'status' => 'aktif',
            'password' => Hash::make('password123'),
        ]);
        $otherKasir->warungs()->attach($otherWarung->id);

        $this->actingAs($owner)
            ->get(route('manajemen-kasir.index'))
            ->assertOk()
            ->assertDontSee('Kasir Orang Lain')
            ->assertDontSee('other-kasir@example.test');

        $this->actingAs($owner)
            ->get(route('manajemen-kasir.edit', $otherKasir))
            ->assertNotFound();

        $this->post(route('manajemen-kasir.store'), [
            'nama' => 'Kasir Tidak Valid',
            'email' => 'invalid-branch@example.test',
            'id_kasir' => $otherWarung->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'aktif',
        ])->assertSessionHasErrors('id_kasir');

        $this->assertDatabaseMissing('user', ['email' => 'invalid-branch@example.test']);
    }

    public function test_transaction_history_includes_all_categories_and_shows_profit_statistics(): void
    {
        [$owner, $warung] = $this->createOwnerAndBranch();
        $cash = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 100000,
        ]);
        $wallet = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'DANA',
            'status' => 'ewallet',
            'saldo' => 50000,
        ]);
        Transaksi::create([
            'id_kas_warung' => $cash->id,
            'total' => 20000,
            'metode_pembayaran' => 'cash',
            'tanggal' => today(),
            'jenis' => 'masuk',
            'kategori' => 'transaksi_customer',
            'biaya_admin' => 300,
        ]);
        Transaksi::create([
            'id_kas_warung' => $wallet->id,
            'total' => 10000,
            'metode_pembayaran' => 'ewallet',
            'tanggal' => today(),
            'jenis' => 'keluar',
            'kategori' => 'transaksi_brilink',
        ]);
        Transaksi::create([
            'id_kas_warung' => $cash->id,
            'total' => 5000,
            'metode_pembayaran' => 'cash',
            'tanggal' => today(),
            'jenis' => 'masuk',
            'kategori' => 'penjualan_item',
            'biaya_admin' => 50,
        ]);

        $this->actingAs($owner)
            ->withSession(['id_warung' => $warung->id])
            ->get(route('kas.index'))
            ->assertOk()
            ->assertSee('Lihat Detail Cash');

        $this->actingAs($owner)
            ->withSession(['id_warung' => $warung->id])
            ->get(route('transaksi.index'))
            ->assertOk()
            ->assertSee('Total Keuntungan')
            ->assertSee('Rp 350')
            ->assertSee('Transaksi Customer')
            ->assertSee('Transaksi BRILink')
            ->assertSee('Penjualan Item')
            ->assertSee('Tunai')
            ->assertSee('E-Wallet')
            ->assertDontSee('Jual Item');
    }

    private function createOwnerAndBranch(string $email = 'owner@example.test'): array
    {
        $owner = User::create([
            'nama' => 'Owner',
            'email' => $email,
            'role' => 'admin',
            'status' => 'aktif',
            'password' => Hash::make('password123'),
        ]);
        $warung = Kasir::create([
            'id_user' => $owner->id,
            'nama_warung' => 'Cabang Owner',
            'modal' => 0,
        ]);

        return [$owner, $warung];
    }
}
