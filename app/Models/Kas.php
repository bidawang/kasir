<?php
// app/Models/Kas.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kas extends Model
{
    use HasFactory;

    protected $table = 'kas';

    protected $fillable = [
        'id_warung',
        'jenis_kas', // Menambahkan kolom baru
        'saldo',
    ];

    public function warung()
    {
        return $this->belongsTo(Kasir::class, 'id_warung');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_kas_warung');
    }

    public function detailKas()
    {
        return $this->hasMany(DetailKas::class, 'id_kas_warung');
    }
}
