<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            // Bank Konvensional & Syariah
            ['kode' => 'BRI', 'nama' => 'Bank Rakyat Indonesia', 'tipe' => 'bank'],
            ['kode' => 'BCA', 'nama' => 'Bank Central Asia', 'tipe' => 'bank'],
            ['kode' => 'MANDIRI', 'nama' => 'Bank Mandiri', 'tipe' => 'bank'],
            ['kode' => 'BNI', 'nama' => 'Bank Negara Indonesia', 'tipe' => 'bank'],
            ['kode' => 'BSI', 'nama' => 'Bank Syariah Indonesia', 'tipe' => 'bank'],
            ['kode' => 'CIMB', 'nama' => 'Bank CIMB Niaga', 'tipe' => 'bank'],
            ['kode' => 'DANAMON', 'nama' => 'Bank Danamon', 'tipe' => 'bank'],
            ['kode' => 'PERMATA', 'nama' => 'Bank Permata', 'tipe' => 'bank'],
            ['kode' => 'BTN', 'nama' => 'Bank Tabungan Negara', 'tipe' => 'bank'],
            ['kode' => 'BTPN', 'nama' => 'Bank BTPN / Jenius', 'tipe' => 'bank'],
            ['kode' => 'JAGO', 'nama' => 'Bank Jago', 'tipe' => 'bank'],
            ['kode' => 'SEABANK', 'nama' => 'SeaBank Indonesia', 'tipe' => 'bank'],
            ['kode' => 'NEO', 'nama' => 'Bank Neo Commerce', 'tipe' => 'bank'],
            
            // E-Wallet Terpopuler
            ['kode' => 'DANA', 'nama' => 'DANA Indonesia', 'tipe' => 'ewallet'],
            ['kode' => 'GOPAY', 'nama' => 'GoPay', 'tipe' => 'ewallet'],
            ['kode' => 'OVO', 'nama' => 'OVO', 'tipe' => 'ewallet'],
            ['kode' => 'SHOPEEPAY', 'nama' => 'ShopeePay', 'tipe' => 'ewallet'],
            ['kode' => 'LINKAJA', 'nama' => 'LinkAja', 'tipe' => 'ewallet'],
        ];

        foreach ($banks as $bank) {
            Bank::firstOrCreate(['kode' => $bank['kode']], $bank);
        }
    }
}
