<?php

namespace Database\Seeders;

use App\Models\Kas;
use App\Models\Kasir;
use App\Models\LaporanHarian;
use App\Models\ItemJualan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KasBrilinkSampleSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::whereIn('role', ['admin', 'owner'])->first() ?? User::first();
        if (!$owner) {
            return;
        }

        $warung = Kasir::firstOrCreate(
            ['id_user' => $owner->id],
            [
                'nama_warung' => 'Agen BRILink Utama',
                'alamat' => 'Jl. Pasar Sentral No. 10',
                'no_telepon' => '08123456789',
                'bank_utama' => 'BRI',
                'modal' => 0,
            ]
        );

        $idWarung = $warung->id;

        // Hubungkan karyawan kasir ke warung ini
        $userKasir = User::where('role', 'kasir')->first();
        if ($userKasir) {
            $userKasir->warungs()->syncWithoutDetaching([$idWarung]);
        }

        // 1. Kas Tunai (Uang Tunai Transaksi)
        Kas::updateOrCreate(
            ['id_warung' => $idWarung, 'status' => 'cash'],
            [
                'jenis_kas' => 'Kas Tunai Transaksi',
                'saldo' => 19250000,
                'is_primary' => true,
            ]
        );

        // 2. Daftar Rekening E-Wallet & Bank
        $daftarKas = [
            ['jenis_kas' => 'EDC', 'saldo' => 60941000, 'status' => 'bank', 'is_primary' => true],
            ['jenis_kas' => 'ATM UMI', 'saldo' => 827000, 'status' => 'bank', 'is_primary' => false],
            ['jenis_kas' => 'BNI', 'saldo' => 541000, 'status' => 'bank', 'is_primary' => false],
            ['jenis_kas' => 'Mandiri', 'saldo' => 0, 'status' => 'bank', 'is_primary' => false],
            ['jenis_kas' => 'Sea Bank', 'saldo' => 253000, 'status' => 'bank', 'is_primary' => false],
            ['jenis_kas' => 'Dana', 'saldo' => 2107000, 'status' => 'ewallet', 'is_primary' => false],
            ['jenis_kas' => 'Gopay', 'saldo' => 74000, 'status' => 'ewallet', 'is_primary' => false],
            ['jenis_kas' => 'Super Bank', 'saldo' => 2478000, 'status' => 'bank', 'is_primary' => false],
            ['jenis_kas' => 'Dana Payz', 'saldo' => 208000, 'status' => 'ewallet', 'is_primary' => false],
            ['jenis_kas' => 'Voucher', 'saldo' => 27000, 'status' => 'ewallet', 'is_primary' => false],
        ];

        foreach ($daftarKas as $item) {
            Kas::updateOrCreate(
                ['id_warung' => $idWarung, 'jenis_kas' => $item['jenis_kas']],
                [
                    'saldo' => $item['saldo'],
                    'status' => $item['status'],
                    'is_primary' => $item['is_primary'],
                ]
            );
        }

        // 3. Item Jualan: Parfum
        ItemJualan::firstOrCreate(
            ['id_warung' => $idWarung, 'nama_item' => 'Parfum'],
            [
                'harga_beli' => 50000,
                'harga_jual' => 68000,
                'jumlah' => 15,
                'jenis' => 'fisik',
            ]
        );

        // 4. Laporan Harian Angsulan
        $userKasir = User::where('role', 'kasir')->first() ?? User::first();
        $userOwner = User::where('role', 'admin')->first() ?? $userKasir;

        LaporanHarian::updateOrCreate(
            [
                'id_warung' => $idWarung,
                'tanggal' => now()->toDateString(),
            ],
            [
                'id_user' => $userKasir->id,
                'id_pelapor' => $userOwner->id,
                'rincian_fisik' => [
                    '100' => 20,    // 2.000
                    '200' => 25,    // 5.000 (total koin = 7.000)
                    '500' => 0,
                    '1000' => 5,    // 5.000
                    '2000' => 32,   // 64.000
                    '5000' => 74,   // 370.000
                    '10000' => 30,  // 300.000
                    '20000' => 10,  // 200.000
                    '50000' => 180, // 9.000.000
                    '100000' => 93, // 9.300.000
                ],
                'saldo_fisik' => 19250000,
                'saldo_sistem' => 19250000,
                'selisih' => 0,
                'catatan' => 'Rekap fisik kasir & angsulan harian lancar',
            ]
        );
        // 5. Transaksi Riil BRILink di Database
        $kategoriSetor = \App\Models\KategoriTransaksi::where('jenis_aliran', 'masuk')->first() ?? \App\Models\KategoriTransaksi::first();
        $kategoriTarik = \App\Models\KategoriTransaksi::where('jenis_aliran', 'keluar')->first() ?? $kategoriSetor;
        $kasTunai = Kas::where('id_warung', $idWarung)->where('status', 'cash')->first();
        $kasEdc = Kas::where('id_warung', $idWarung)->where('jenis_kas', 'EDC')->first() ?? Kas::where('id_warung', $idWarung)->where('status', '!=', 'cash')->first();
        $itemParfum = ItemJualan::where('id_warung', $idWarung)->where('nama_item', 'Parfum')->first();

        // Hapus dummy transaksi lama jika ada
        \App\Models\Transaksi::whereHas('kas', fn($q) => $q->where('id_warung', $idWarung))->delete();

        // A. Transaksi Kemarin (Total Keuntungan = Rp 3.162.000)
        $waktuKemarin = now()->subDay()->setTime(15, 30, 0);
        \App\Models\Transaksi::create([
            'kode_transaksi'    => 'TRX-' . $waktuKemarin->format('Ymd') . '-001',
            'id_user'           => $userKasir->id,
            'id_kas_warung'     => $kasTunai->id,
            'id_kas_sumber'     => $kasEdc->id,
            'id_kategori'       => $kategoriSetor->id,
            'jenis'             => 'masuk',
            'kategori'          => 'transaksi_customer',
            'metode_transaksi'  => 'kartu',
            'metode_pembayaran' => 'cash',
            'nominal'           => 150000000,
            'biaya_admin'       => 3500000,
            'biaya_admin_bank'  => 338000,
            'keuntungan'        => 3162000,
            'kas_masuk'         => 153500000,
            'kas_keluar'        => 0,
            'total'             => 153500000,
            'tanggal'           => $waktuKemarin->toDateString(),
            'keterangan'        => 'Akumulasi Transaksi BRILink Kemarin',
            'created_at'        => $waktuKemarin,
            'updated_at'        => $waktuKemarin,
        ]);

        // B. Transaksi Hari Ini - Shift Siang (Keuntungan = Rp 65.000)
        $waktuSiang = now()->setTime(11, 15, 0);
        \App\Models\Transaksi::create([
            'kode_transaksi'    => 'TRX-' . $waktuSiang->format('Ymd') . '-002',
            'id_user'           => $userKasir->id,
            'id_kas_warung'     => $kasTunai->id,
            'id_kas_sumber'     => $kasEdc->id,
            'id_kategori'       => $kategoriSetor->id,
            'jenis'             => 'masuk',
            'kategori'          => 'transaksi_customer',
            'metode_transaksi'  => 'kartu',
            'metode_pembayaran' => 'cash',
            'nominal'           => 5000000,
            'biaya_admin'       => 75000,
            'biaya_admin_bank'  => 10000,
            'keuntungan'        => 65000,
            'kas_masuk'         => 5075000,
            'kas_keluar'        => 0,
            'total'             => 5075000,
            'tanggal'           => $waktuSiang->toDateString(),
            'keterangan'        => 'Setor Tunai Nasabah - Shift Siang',
            'created_at'        => $waktuSiang,
            'updated_at'        => $waktuSiang,
        ]);

        // C. Transaksi Hari Ini - Shift Malam (Keuntungan = Rp 127.000)
        $waktuMalam = now()->setTime(19, 45, 0);
        \App\Models\Transaksi::create([
            'kode_transaksi'    => 'TRX-' . $waktuMalam->format('Ymd') . '-003',
            'id_user'           => $userKasir->id,
            'id_kas_warung'     => $kasTunai->id,
            'id_kas_sumber'     => $kasEdc->id,
            'id_kategori'       => $kategoriTarik->id,
            'jenis'             => 'keluar',
            'kategori'          => 'transaksi_customer',
            'metode_transaksi'  => 'kartu',
            'metode_pembayaran' => 'cash',
            'nominal'           => 8000000,
            'biaya_admin'       => 140000,
            'biaya_admin_bank'  => 13000,
            'keuntungan'        => 127000,
            'kas_masuk'         => 0,
            'kas_keluar'        => 8000000,
            'total'             => 8000000,
            'tanggal'           => $waktuMalam->toDateString(),
            'keterangan'        => 'Tarik Tunai Nasabah - Shift Malam',
            'created_at'        => $waktuMalam,
            'updated_at'        => $waktuMalam,
        ]);

        // D. Transaksi Penjualan Barang Toko (Duit Parfum = Rp 68.000)
        $waktuParfum = now()->setTime(14, 20, 0);
        \App\Models\Transaksi::create([
            'kode_transaksi'    => 'TRX-' . $waktuParfum->format('Ymd') . '-004',
            'id_user'           => $userKasir->id,
            'id_kas_warung'     => $kasTunai->id,
            'id_kas_sumber'     => null,
            'id_kategori'       => null,
            'id_item_jualan'    => $itemParfum ? $itemParfum->id : null,
            'jenis'             => 'masuk',
            'kategori'          => 'kas_toko',
            'metode_transaksi'  => 'cash',
            'metode_pembayaran' => 'cash',
            'nominal'           => 68000,
            'biaya_admin'       => 0,
            'biaya_admin_bank'  => 0,
            'keuntungan'        => 18000,
            'kas_masuk'         => 68000,
            'kas_keluar'        => 0,
            'total'             => 68000,
            'nama_item'         => 'Parfum',
            'harga_item'        => 68000,
            'tanggal'           => $waktuParfum->toDateString(),
            'keterangan'        => 'Penjualan Parfum Toko (1 pcs)',
            'created_at'        => $waktuParfum,
            'updated_at'        => $waktuParfum,
        ]);
    }
}
