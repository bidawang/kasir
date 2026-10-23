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

    protected function casts(): array
    {
        return [
            'pecahan' => 'integer',
            'jumlah' => 'integer',
        ];
    }

    public function kas()
    {
        return $this->belongsTo(Kas::class, 'id_kas_warung');
    }
}