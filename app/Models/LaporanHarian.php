<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanHarian extends Model
{
    protected $table = 'laporan_harian';

    protected $fillable = [
        'id_warung',
        'id_user',
        'id_pelapor',
        'tanggal',
        'rincian_fisik',
        'saldo_fisik',
        'saldo_sistem',
        'selisih',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'rincian_fisik' => 'array',
            'saldo_fisik' => 'integer',
            'saldo_sistem' => 'integer',
            'selisih' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_pelapor');
    }

    public function warung()
    {
        return $this->belongsTo(Kasir::class, 'id_warung');
    }
}
