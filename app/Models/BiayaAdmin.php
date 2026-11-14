<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiayaAdmin extends Model
{
    use HasFactory;

    protected $table = 'biaya_admin';

    protected $fillable = [
        'id_warung',
        'id_kategori',
        'id_kas',
        'range_awal',
        'range_akhir',
        'biaya_admin',
        'biaya_admin_bank',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'range_awal' => 'integer',
            'range_akhir' => 'integer',
            'biaya_admin' => 'integer',
            'biaya_admin_bank' => 'integer',
        ];
    }

    public function warung(): BelongsTo
    {
        return $this->belongsTo(Kasir::class, 'id_warung');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriTransaksi::class, 'id_kategori');
    }

    public function kas(): BelongsTo
    {
        return $this->belongsTo(Kas::class, 'id_kas');
    }
}
