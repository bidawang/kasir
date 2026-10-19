<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemJualan extends Model
{
    use HasFactory;

    // Menyesuaikan nama tabel karena tidak menggunakan bentuk plural bahasa Inggris
    protected $table = 'item_jualan';

    protected $fillable = [
        'nama_item',
        'harga_jual',
        'id_warung'
    ];

    public function warung()
    {
        return $this->belongsTo(Kasir::class, 'id_warung');
    }
}