<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'plafon_pinjaman',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'plafon_pinjaman' => 'decimal:2',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
