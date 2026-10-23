<?php
// app/Models/Transaksi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'id_kas_warung',
        'id_kas_sumber',
        'id_kategori',
        'total',
        'keterangan',
        'biaya_admin',
        'jenis',
        'kategori',
        'id_item_jualan',
        'nama_item',
        'harga_item',
        'metode_pembayaran',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'biaya_admin' => 'integer',
            'harga_item' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function kas()
    {
        return $this->belongsTo(Kas::class, 'id_kas_warung');
    }

    public function kasSumber()
    {
        return $this->belongsTo(Kas::class, 'id_kas_sumber');
    }

    public function kategoriTransaksi()
    {
        return $this->belongsTo(KategoriTransaksi::class, 'id_kategori');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi_kas');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi_kas');
    }

    public function itemJualan()
    {
        return $this->belongsTo(ItemJualan::class, 'id_item_jualan');
    }

}
