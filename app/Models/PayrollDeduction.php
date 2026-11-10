<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'member_id',
        'nominal_simpanan_wajib',
        'nominal_angsuran_pinjaman',
        'total_potongan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'nominal_simpanan_wajib' => 'decimal:2',
            'nominal_angsuran_pinjaman' => 'decimal:2',
            'total_potongan' => 'decimal:2',
        ];
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
