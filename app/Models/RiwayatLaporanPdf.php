<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatLaporanPdf extends Model
{
    use HasFactory;

    protected $table = 'riwayat_laporan_pdf';

    protected $fillable = [
        'id_warung',
        'id_user',
        'nomor_laporan',
        'judul',
        'start_date',
        'end_date',
        'id_kategori',
        'kategori_label',
        'total_masuk',
        'total_keluar',
        'total_keuntungan',
        'total_admin_toko',
        'total_admin_bank',
        'total_transaksi',
        'catatan',
        'filter_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_masuk' => 'decimal:2',
            'total_keluar' => 'decimal:2',
            'total_keuntungan' => 'decimal:2',
            'total_admin_toko' => 'decimal:2',
            'total_admin_bank' => 'decimal:2',
            'total_transaksi' => 'integer',
            'filter_snapshot' => 'array',
        ];
    }

    public function warung()
    {
        return $this->belongsTo(Kasir::class, 'id_warung');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kategoriTransaksi()
    {
        return $this->belongsTo(KategoriTransaksi::class, 'id_kategori');
    }
}
