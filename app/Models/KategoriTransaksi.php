<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriTransaksi extends Model
{
    use HasFactory;

    protected $table = 'kategori_transaksi';

    protected $fillable = [
        'kode',
        'nama_kategori',
        'jenis_transaksi',
        'metode_transaksi',
        'jenis_aliran',
        'jenis_mutasi_akun',
        'deskripsi',
        'icon',
        'badge_color',
        'urutan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function biayaAdmin(): HasMany
    {
        return $this->hasMany(BiayaAdmin::class, 'id_kategori');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_kategori');
    }

    /**
     * Dapatkan nama kategori yang disesuaikan secara dinamis dengan nama Bank Utama
     */
    public function getNamaDinamis(?string $bankUtama = 'BRI'): string
    {
        $bank = strtoupper($bankUtama ?: 'BRI');
        return str_replace(['Bank Utama', 'Sesama Bank'], [$bank, 'Sesama ' . $bank], $this->nama_kategori);
    }
}
