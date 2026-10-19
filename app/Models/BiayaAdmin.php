<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaAdmin extends Model
{
    use HasFactory;

    // Menyesuaikan nama tabel
    protected $table = 'biaya_admin';

    protected $fillable = [
        'range_awal',
        'range_akhir',
        'biaya_admin',
        'id_warung'
    ];

    public function warung()
    {
        return $this->belongsTo(Kasir::class, 'id_warung');
    }
}