<?php

namespace Tests\Feature;

use App\Models\BiayaAdmin;
use App\Models\DetailKas;
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

    public function test_virtual_item_sale_uses_type_specific_fee_and_debits_selected_wallet(): void
    {
        [$user, $warung] = $this->signInForWarung();
        $item = ItemJualan::create([
            'id_warung' => $warung->id,
            'nama_item' => 'Pulsa',
            'harga_jual' => 12000,
            'jenis' => 'virtual',
            'jumlah' => 5,
        ]);
        $kas = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 5000,
        ]);
        DetailKas::create([
            'id_kas_warung' => $kas->id,
            'pecahan' => 500,
            'jumlah' => 1,
        ]);
        $wallet = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Wallet',
            'status' => 'ewallet',
            'saldo' => 20000,
        ]);
        $feeRange = BiayaAdmin::create([
            'id_warung' => $warung->id,
            'range_awal' => 10000,
            'range_akhir' => 15000,
            'biaya_admin' => 2500,
            'jenis' => 'virtual',
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);

        $this->get(route('transaksi.menu'))
            ->assertOk()
            ->assertSee('Setor Tunai')
            ->assertSee('Tarik Tunai')
            ->assertSee('Kas Masuk')
            ->assertSee('Kas Keluar')
            ->assertSee('Jual Item');
        $this->get(route('transaksi.penjualan-item.create'))
            ->assertOk()
            ->assertSee('Cari nama item')
            ->assertSee('Ada uang kembalian');
        $this->post(route('transaksi.penjualan-item.store'), [
            'id_item_jualan' => $item->id,
            'id_kas' => $kas->id,
            'harga_item' => 12000,
            'id_kas_sumber' => $wallet->id,
            'denominations_json' => json_encode([
                ['pecahan' => 10000, 'jumlah' => 1],
                ['pecahan' => 5000, 'jumlah' => 1],
            ]),
            'change_denominations_json' => json_encode([['pecahan' => 500, 'jumlah' => 1]]),
            'change_total' => 500,
        ])->assertRedirect(route('transaksi.index'));

        $transaksi = Transaksi::where('kategori', 'penjualan_item')->firstOrFail();
        $this->assertSame('masuk', $transaksi->jenis);
        $this->assertSame(12000, $transaksi->harga_item);
        $this->assertSame(2500, $transaksi->biaya_admin);
        $this->assertSame(14500, $transaksi->total);
        $this->assertSame('Pulsa', $transaksi->nama_item);
        $this->assertSame(19500, $kas->fresh()->saldo);
        $this->assertSame($wallet->id, $transaksi->id_kas_sumber);
        $this->assertSame(8000, $wallet->fresh()->saldo);
        $this->assertNull($item->fresh()->jumlah);

        $this->get(route('transaksi.index'))->assertOk()->assertSee('Penjualan Item');

        $this->put(route('biaya-admin.update', $feeRange->id), [
            'jenis' => 'virtual',
            'range_awal' => 11000,
            'range_akhir' => 15000,
            'biaya_admin' => 3000,
        ])->assertRedirect(route('biaya-admin.index'));

        $this->post(route('biaya-admin.store'), [
            'jenis' => 'virtual',
            'range_awal' => 12000,
            'range_akhir' => 20000,
            'biaya_admin' => 4000,
        ])->assertSessionHasErrors('range_awal');

        $this->delete(route('biaya-admin.destroy', $feeRange->id))
            ->assertRedirect(route('biaya-admin.index'));
    }

    public function test_physical_item_sale_decrements_stock_and_changes_cash_denominations(): void
    {
        [$user, $warung] = $this->signInForWarung();
        $item = ItemJualan::create([
            'id_warung' => $warung->id,
            'nama_item' => 'Kartu',
            'harga_jual' => 5000,
            'jenis' => 'fisik',
            'jumlah' => 2,
        ]);
        $kas = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 0,
        ]);
        DetailKas::create([
            'id_kas_warung' => $kas->id,
            'pecahan' => 2000,
            'jumlah' => 2,
        ]);
        BiayaAdmin::create([
            'id_warung' => $warung->id,
            'range_awal' => 5000,
            'range_akhir' => 5000,
            'biaya_admin' => 1000,
            'jenis' => 'fisik',
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);
        $this->post(route('transaksi.penjualan-item.store'), [
            'id_item_jualan' => $item->id,
            'id_kas' => $kas->id,
            'denominations_json' => json_encode([['pecahan' => 10000, 'jumlah' => 1]]),
            'change_denominations_json' => json_encode([['pecahan' => 2000, 'jumlah' => 2]]),
            'change_total' => 4000,
        ])->assertRedirect(route('transaksi.index'));

        $this->assertSame(1, $item->fresh()->jumlah);
        $this->assertSame(6000, $kas->fresh()->saldo);
        $this->assertSame(0, DetailKas::where('id_kas_warung', $kas->id)->where('pecahan', 2000)->value('jumlah'));
        $this->assertDatabaseHas('transaksi', [
            'id_item_jualan' => $item->id,
            'harga_item' => 5000,
            'biaya_admin' => 1000,
            'total' => 6000,
            'id_kas_sumber' => null,
        ]);
    }

    public function test_item_sale_accepts_manual_admin_fee_when_no_range_matches(): void
    {
        [$user, $warung] = $this->signInForWarung();
        $item = ItemJualan::create([
            'id_warung' => $warung->id,
            'nama_item' => 'Pulsa',
            'harga_jual' => 10000,
            'jenis' => 'virtual',
        ]);
        $cash = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 0,
        ]);
        $wallet = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Wallet',
            'status' => 'ewallet',
            'saldo' => 20000,
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);
        $this->post(route('transaksi.penjualan-item.store'), [
            'id_item_jualan' => $item->id,
            'id_kas' => $cash->id,
            'id_kas_sumber' => $wallet->id,
            'harga_item' => 10000,
            'biaya_admin_manual' => 500,
            'denominations_json' => json_encode([
                ['pecahan' => 10000, 'jumlah' => 1],
                ['pecahan' => 500, 'jumlah' => 1],
            ]),
            'change_denominations_json' => '[]',
            'change_total' => 0,
        ])->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseHas('transaksi', [
            'id_item_jualan' => $item->id,
            'biaya_admin' => 500,
            'total' => 10500,
        ]);
        $this->assertSame(10000, $wallet->fresh()->saldo);
        $this->assertSame(10500, $cash->fresh()->saldo);
    }

    public function test_fee_ranges_can_overlap_between_types_but_not_within_same_type(): void
    {
        [$user, $warung] = $this->signInForWarung();
        BiayaAdmin::create([
            'id_warung' => $warung->id,
            'range_awal' => 10000,
            'range_akhir' => 15000,
            'biaya_admin' => 2500,
            'jenis' => 'bank',
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);
        $this->post(route('biaya-admin.store'), [
            'jenis' => 'virtual',
            'range_awal' => 10000,
            'range_akhir' => 15000,
            'biaya_admin' => 3000,
        ])->assertRedirect(route('biaya-admin.index'));

        $this->post(route('biaya-admin.store'), [
            'jenis' => 'bank',
            'range_awal' => 14000,
            'range_akhir' => 20000,
            'biaya_admin' => 3000,
        ])->assertSessionHasErrors('range_awal');
    }

    public function test_bank_fees_can_be_scoped_to_a_wallet_and_reused_for_another_wallet(): void
    {
        [$user, $warung] = $this->signInForWarung();
        $bankA = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Bank A',
            'status' => 'ewallet',
            'saldo' => 10000,
        ]);
        $bankB = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Bank B',
            'status' => 'ewallet',
            'saldo' => 10000,
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);
        $this->post(route('biaya-admin.store'), [
            'jenis' => 'bank',
            'id_kas' => $bankA->id,
            'range_awal' => 5000,
            'range_akhir' => 10000,
            'biaya_admin' => 1000,
        ])->assertRedirect(route('biaya-admin.index'));

        $this->post(route('biaya-admin.store'), [
            'jenis' => 'bank',
            'id_kas' => $bankB->id,
            'range_awal' => 5000,
            'range_akhir' => 10000,
            'biaya_admin' => 1500,
        ])->assertRedirect(route('biaya-admin.index'));

        $this->assertDatabaseHas('biaya_admin', [
            'id_warung' => $warung->id,
            'jenis' => 'bank',
            'id_kas' => $bankA->id,
            'biaya_admin' => 1000,
        ]);
    }

    public function test_setor_uses_bank_fee_range_to_calculate_wallet_transfer(): void
    {
        [$user, $warung] = $this->signInForWarung();
        $cash = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 0,
        ]);
        $wallet = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Wallet',
            'status' => 'ewallet',
            'saldo' => 20000,
        ]);
        BiayaAdmin::create([
            'id_warung' => $warung->id,
            'range_awal' => 10000,
            'range_akhir' => 10000,
            'biaya_admin' => 1000,
            'jenis' => 'bank',
            'id_kas' => $wallet->id,
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);
        $this->post(route('transaksi.store'), [
            'jenis' => 'masuk',
            'id_kas' => $cash->id,
            'id_kas_tujuan' => $wallet->id,
            'total' => 9000,
            'denominations_json' => json_encode([['pecahan' => 10000, 'jumlah' => 1]]),
            'change_denominations_json' => '[]',
            'change_total' => 0,
        ])->assertRedirect(route('transaksi.index'));

        $this->assertSame(10000, $cash->fresh()->saldo);
        $this->assertSame(11000, $wallet->fresh()->saldo);
        $this->assertDatabaseHas('transaksi', [
            'kategori' => 'transaksi_customer',
            'jenis' => 'masuk',
            'total' => 9000,
            'biaya_admin' => 1000,
        ]);
    }

    public function test_tarik_uses_bank_fee_range_to_calculate_wallet_credit(): void
    {
        [$user, $warung] = $this->signInForWarung();
        $cash = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai',
            'status' => 'cash',
            'saldo' => 10000,
        ]);
        $wallet = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Wallet',
            'status' => 'ewallet',
            'saldo' => 0,
        ]);
        DetailKas::create([
            'id_kas_warung' => $cash->id,
            'pecahan' => 10000,
            'jumlah' => 1,
        ]);
        BiayaAdmin::create([
            'id_warung' => $warung->id,
            'range_awal' => 10000,
            'range_akhir' => 10000,
            'biaya_admin' => 1000,
            'jenis' => 'bank',
            'id_kas' => $wallet->id,
        ]);

        $this->actingAs($user)->withSession(['id_warung' => $warung->id]);
        $this->post(route('transaksi.store'), [
            'jenis' => 'keluar',
            'id_kas' => $cash->id,
            'id_kas_tujuan' => $wallet->id,
            'total' => 11000,
            'denominations_json' => json_encode([['pecahan' => 10000, 'jumlah' => 1]]),
            'change_denominations_json' => '[]',
            'change_total' => 0,
        ])->assertRedirect(route('transaksi.index'));

        $this->assertSame(0, $cash->fresh()->saldo);
        $this->assertSame(11000, $wallet->fresh()->saldo);
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
