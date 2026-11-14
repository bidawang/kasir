<?php

namespace Database\Seeders;

use App\Models\KategoriTransaksi;
use Illuminate\Database\Seeder;

class KategoriTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'kode'             => 'tf_sesama_kartu',
                'nama_kategori'    => 'Transfer Sesama Bank (Pakai Kartu / EDC)',
                'jenis_transaksi'  => 'transfer',
                'metode_transaksi' => 'kartu',
                'jenis_aliran'     => 'masuk',
                'jenis_mutasi_akun' => 'keluar',
                'deskripsi'        => 'Nasabah setor uang tunai untuk transfer sesama rekening via mesin EDC',
                'icon'             => 'credit-card',
                'badge_color'      => 'blue',
                'urutan'           => 1,
            ],
            [
                'kode'             => 'tf_sesama_app',
                'nama_kategori'    => 'Transfer Sesama Bank (Tanpa Kartu / Aplikasi)',
                'jenis_transaksi'  => 'transfer',
                'metode_transaksi' => 'tanpa_kartu',
                'jenis_aliran'     => 'masuk',
                'jenis_mutasi_akun' => 'keluar',
                'deskripsi'        => 'Nasabah setor tunai untuk transfer sesama rekening via mobile/internet banking',
                'icon'             => 'device-phone-mobile',
                'badge_color'      => 'indigo',
                'urutan'           => 2,
            ],
            [
                'kode'             => 'tf_bank_lain',
                'nama_kategori'    => 'Transfer Antar Bank Lain',
                'jenis_transaksi'  => 'transfer',
                'metode_transaksi' => 'umum',
                'jenis_aliran'     => 'masuk',
                'jenis_mutasi_akun' => 'keluar',
                'deskripsi'        => 'Nasabah setor uang tunai untuk kirim dana ke rekening bank lain',
                'icon'             => 'arrow-right-circle',
                'badge_color'      => 'amber',
                'urutan'           => 3,
            ],
            [
                'kode'             => 'tarik_utama_kartu',
                'nama_kategori'    => 'Tarik Tunai Bank Utama (Pakai Kartu / EDC)',
                'jenis_transaksi'  => 'tarik_tunai',
                'metode_transaksi' => 'kartu',
                'jenis_aliran'     => 'keluar',
                'jenis_mutasi_akun' => 'masuk',
                'deskripsi'        => 'Nasabah gesek kartu di EDC dan kasir menyerahkan uang fisik',
                'icon'             => 'banknotes',
                'badge_color'      => 'rose',
                'urutan'           => 4,
            ],
            [
                'kode'             => 'tarik_utama_app',
                'nama_kategori'    => 'Tarik Tunai Bank Utama (Tanpa Kartu)',
                'jenis_transaksi'  => 'tarik_tunai',
                'metode_transaksi' => 'tanpa_kartu',
                'jenis_aliran'     => 'keluar',
                'jenis_mutasi_akun' => 'masuk',
                'deskripsi'        => 'Tarik tunai tanpa kartu via kode reservasi tarik tunai / transfer ke rekening agen',
                'icon'             => 'qr-code',
                'badge_color'      => 'rose',
                'urutan'           => 5,
            ],
            [
                'kode'             => 'tarik_bank_lain',
                'nama_kategori'    => 'Tarik Tunai Bank Lain (Kartu EDC)',
                'jenis_transaksi'  => 'tarik_tunai',
                'metode_transaksi' => 'kartu',
                'jenis_aliran'     => 'keluar',
                'jenis_mutasi_akun' => 'masuk',
                'deskripsi'        => 'Penarikan tunai dari kartu bank lain melalui jaringan ATM Bersama / Prima di EDC',
                'icon'             => 'building-library',
                'badge_color'      => 'red',
                'urutan'           => 6,
            ],
            [
                'kode'             => 'ewallet_topup',
                'nama_kategori'    => 'Top Up E-Wallet (DANA, Gopay, OVO, ShopeePay)',
                'jenis_transaksi'  => 'ewallet',
                'metode_transaksi' => 'aplikasi',
                'jenis_aliran'     => 'masuk',
                'jenis_mutasi_akun' => 'keluar',
                'deskripsi'        => 'Nasabah bayar uang tunai untuk isi saldo dompet digital',
                'icon'             => 'wallet',
                'badge_color'      => 'emerald',
                'urutan'           => 7,
            ],
            [
                'kode'             => 'ewallet_tarik',
                'nama_kategori'    => 'Tarik Tunai Saldo E-Wallet (QRIS / Transfer)',
                'jenis_transaksi'  => 'ewallet',
                'metode_transaksi' => 'qris',
                'jenis_aliran'     => 'keluar',
                'jenis_mutasi_akun' => 'masuk',
                'deskripsi'        => 'Nasabah scan QRIS agen / transfer saldo dan kasir memberikan uang tunai',
                'icon'             => 'arrow-down-tray',
                'badge_color'      => 'orange',
                'urutan'           => 8,
            ],
            [
                'kode'             => 'briva_va',
                'nama_kategori'    => 'Pembayaran Virtual Account (VA / BRIVA)',
                'jenis_transaksi'  => 'ppob',
                'metode_transaksi' => 'aplikasi',
                'jenis_aliran'     => 'masuk',
                'jenis_mutasi_akun' => 'keluar',
                'deskripsi'        => 'Nasabah bayar tunai untuk tagihan Virtual Account (BRIVA / Mandiri VA / BCA VA)',
                'icon'             => 'receipt-percent',
                'badge_color'      => 'purple',
                'urutan'           => 9,
            ],
            [
                'kode'             => 'ppob_tagihan',
                'nama_kategori'    => 'Listrik PLN, Pulsa & Data, Wifi, Cicilan',
                'jenis_transaksi'  => 'ppob',
                'metode_transaksi' => 'aplikasi',
                'jenis_aliran'     => 'masuk',
                'jenis_mutasi_akun' => 'keluar',
                'deskripsi'        => 'Pembayaran tagihan bulanan dan pembelian pulsa / token listrik',
                'icon'             => 'bolt',
                'badge_color'      => 'cyan',
                'urutan'           => 10,
            ],
        ];

        foreach ($categories as $cat) {
            KategoriTransaksi::updateOrCreate(['kode' => $cat['kode']], $cat);
        }
    }
}
