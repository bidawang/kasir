<?php
// app/Models/DetailKas.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKas extends Model
{
    use HasFactory;

    protected $table = 'detail_kas';

    protected $fillable = [
        'id_kas_warung',
        'pecahan',
        'jumlah',
    ];

    public function kas()
    {
        return $this->belongsTo(Kas::class, 'id_kas_warung');
    }
}