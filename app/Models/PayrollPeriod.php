<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_periode',
        'nama_periode',
        'tanggal_cutoff',
        'status',
        'total_simpanan_wajib',
        'total_angsuran_pinjaman',
        'total_pemotongan',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_cutoff' => 'date',
            'total_simpanan_wajib' => 'decimal:2',
            'total_angsuran_pinjaman' => 'decimal:2',
            'total_pemotongan' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
