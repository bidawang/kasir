<?php

namespace Tests\Feature;

use App\Models\BiayaAdmin;
use App\Models\ItemJualan;
use App\Models\Kas;
use App\Models\Kasir;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ItemJualanBiayaAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_fee_ranges_are_independent_and_used_when_selling_an_item(): void
    {
        [$user, $warung] = $this->signInForWarung();
        $item = ItemJualan::create([
            'id_warung' => $warung->id,
            'nama_item' => 'Pulsa',
            'harga_jual' => 12000,
        ]);
        $kas = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 5000,
        ]);
        $feeRange = BiayaAdmin::create([
            'id_warung' => $warung->id,
            'range_awal' => 10000,
            'range_akhir' => 15000,
            'biaya_admin' => 2500,
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);

        $this->get(route('transaksi.penjualan-item.create'))->assertOk();
        $this->post(route('transaksi.penjualan-item.store'), [
            'id_item_jualan' => $item->id,
            'id_kas' => $kas->id,
        ])->assertRedirect(route('transaksi.index'));

        $transaksi = Transaksi::where('kategori', 'penjualan_item')->firstOrFail();
        $this->assertSame('masuk', $transaksi->jenis);
        $this->assertSame(12000, $transaksi->harga_item);
        $this->assertSame(2500, $transaksi->biaya_admin);
        $this->assertSame(14500, $transaksi->total);
        $this->assertSame('Pulsa', $transaksi->nama_item);
        $this->assertSame(19500, $kas->fresh()->saldo);

        $this->get(route('transaksi.index'))->assertOk()->assertSee('Penjualan Item');

        $this->put(route('biaya-admin.update', $feeRange->id), [
            'range_awal' => 11000,
            'range_akhir' => 15000,
            'biaya_admin' => 3000,
        ])->assertRedirect(route('biaya-admin.index'));

        $this->post(route('biaya-admin.store'), [
            'range_awal' => 12000,
            'range_akhir' => 20000,
            'biaya_admin' => 4000,
        ])->assertSessionHasErrors('range_awal');

        $this->delete(route('biaya-admin.destroy', $feeRange->id))
            ->assertRedirect(route('biaya-admin.index'));
    }

    private function signInForWarung(): array
    {
        $now = now();
        $userId = DB::table('user')->insertGetId([
            'nama' => 'Operator',
            'nomor_hp' => '08123456789',
            'username' => 'operator',
            'email' => 'operator@example.test',
            'password' => bcrypt('password'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $user = User::findOrFail($userId);
        $warung = Kasir::create([
            'id_user' => $userId,
            'nama_warung' => 'Warung Uji',
            'modal' => 0,
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);

        return [$user, $warung];
    }
}
