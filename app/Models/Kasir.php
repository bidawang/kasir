<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kasir extends Model
{
    use HasFactory;

    protected $table = 'kasir';

    protected $fillable = [
        'id_user',
        'nama_warung',
        'alamat',
        'no_telepon',
        'modal',
        'bank_utama',
        'nomor_rekening_utama',
        'atas_nama_utama',
    ];

    protected function casts(): array
    {
        return [
            'modal' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kasir_user', 'id_kasir', 'id_user')
            ->withTimestamps();
    }

    public function kas(): HasMany
    {
        return $this->hasMany(Kas::class, 'id_warung');
    }
}
