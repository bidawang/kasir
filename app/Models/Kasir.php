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
        'id_area',
        'nama_warung',
        'modal',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kas()
    {
        return $this->hasOne(Kas::class, 'id_warung');
    }
}