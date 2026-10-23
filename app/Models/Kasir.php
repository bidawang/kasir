<?php

// app/Models/Kasir.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasir extends Model
{
    use HasFactory;

    protected $table = 'kasir';

    protected $fillable = [
        'id_user',
        'nama_warung',
        'modal',
    ];

    protected function casts(): array
    {
        return [
            'modal' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kas()
    {
        return $this->hasMany(Kas::class, 'id_warung');
    }
}