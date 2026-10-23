<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferKas extends Model
{
    use HasFactory;

    protected $table = 'transfer_kas';

    protected $fillable = [
        'id_kas_asal',
        'id_kas_tujuan',
        'jumlah',
        'biaya_transfer',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'biaya_transfer' => 'integer',
        ];
    }

    public function kasAsal(): BelongsTo
    {
        return $this->belongsTo(Kas::class, 'id_kas_asal');
    }

    public function kasTujuan(): BelongsTo
    {
        return $this->belongsTo(Kas::class, 'id_kas_tujuan');
    }
}