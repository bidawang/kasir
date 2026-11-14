<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'id_user',
        'id_kas_warung',
        'id_kas_sumber',
        'id_kategori',
        'id_item_jualan',
        'kategori',
        'sub_kategori',
        'metode_transaksi',
        'metode_pembayaran',
        'bank_tujuan',
        'nomor_tujuan',
        'nama_tujuan',
        'nominal',
        'biaya_admin',
        'biaya_admin_bank',
        'keuntungan',
        'kas_masuk',
        'kas_keluar',
        'total',
        'is_profit',
        'jenis',
        'status',
        'nama_item',
        'harga_item',
        'keterangan',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'biaya_admin' => 'integer',
            'biaya_admin_bank' => 'integer',
            'keuntungan' => 'integer',
            'kas_masuk' => 'integer',
            'kas_keluar' => 'integer',
            'total' => 'integer',
            'harga_item' => 'integer',
            'is_profit' => 'boolean',
            'tanggal' => 'date',
        ];
    }

    public function kas(): BelongsTo
    {
        return $this->belongsTo(Kas::class, 'id_kas_warung');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kasSumber(): BelongsTo
    {
        return $this->belongsTo(Kas::class, 'id_kas_sumber');
    }

    public function kategoriTransaksi(): BelongsTo
    {
        return $this->belongsTo(KategoriTransaksi::class, 'id_kategori');
    }

    public function kategoriRelasi(): BelongsTo
    {
        return $this->belongsTo(KategoriTransaksi::class, 'id_kategori');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi_kas');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi_kas');
    }

    public function itemJualan(): BelongsTo
    {
        return $this->belongsTo(ItemJualan::class, 'id_item_jualan');
    }
}
