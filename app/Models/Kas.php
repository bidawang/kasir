<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kas extends Model
{
    use HasFactory;

    protected $table = 'kas';

    protected $fillable = [
        'id_warung',
        'jenis_kas',
        'saldo',
        'status',
        'nomor_rekening',
        'atas_nama',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'saldo' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function warung()
    {
        return $this->belongsTo(Kasir::class, 'id_warung');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_kas_warung');
    }

    public function transaksiSumber(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_kas_sumber');
    }

    public function detailKas()
    {
        return $this->hasMany(DetailKas::class, 'id_kas_warung');
    }

    public function transferKeluar(): HasMany
    {
        return $this->hasMany(TransferKas::class, 'id_kas_asal');
    }

    public function transferMasuk(): HasMany
    {
        return $this->hasMany(TransferKas::class, 'id_kas_tujuan');
    }
}
