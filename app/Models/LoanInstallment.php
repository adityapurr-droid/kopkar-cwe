<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'angsuran_ke',
        'nominal_pokok',
        'nominal_bunga',
        'total_angsuran',
        'sisa_saldo_pokok',
        'jatuh_tempo',
        'status_bayar',
        'tanggal_bayar',
    ];

    protected function casts(): array
    {
        return [
            'nominal_pokok' => 'decimal:2',
            'nominal_bunga' => 'decimal:2',
            'total_angsuran' => 'decimal:2',
            'sisa_saldo_pokok' => 'decimal:2',
            'jatuh_tempo' => 'date',
            'tanggal_bayar' => 'datetime',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
