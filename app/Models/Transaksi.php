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
        'total',
        'metode_pembayaran',
        'keterangan',
        'tanggal',
        'jenis',
    ];

    public function kas()
    {
        return $this->belongsTo(Kas::class, 'id_kas_warung');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi_kas');
    }
}