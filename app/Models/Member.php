<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik_karyawan',
        'no_anggota',
        'nama_lengkap',
        'division_id',
        'position_id',
        'no_hp',
        'alamat',
        'tanggal_bergabung',
        'status_keanggotaan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bergabung' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function payrollDeductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class);
    }

    public function totalSimpananPokok(): float
    {
        $masuk = (float) $this->savings()->where('jenis_simpanan', 'pokok')->where('tipe', 'masuk')->sum('nominal');
        $keluar = (float) $this->savings()->where('jenis_simpanan', 'pokok')->where('tipe', 'keluar')->sum('nominal');

        return $masuk - $keluar;
    }

    public function totalSimpananWajib(): float
    {
        $masuk = (float) $this->savings()->where('jenis_simpanan', 'wajib')->where('tipe', 'masuk')->sum('nominal');
        $keluar = (float) $this->savings()->where('jenis_simpanan', 'wajib')->where('tipe', 'keluar')->sum('nominal');

        return $masuk - $keluar;
    }

    public function totalSimpananSukarela(): float
    {
        $masuk = (float) $this->savings()->where('jenis_simpanan', 'sukarela')->where('tipe', 'masuk')->sum('nominal');
        $keluar = (float) $this->savings()->where('jenis_simpanan', 'sukarela')->where('tipe', 'keluar')->sum('nominal');

        return $masuk - $keluar;
    }

    public function totalSaldoSimpanan(): float
    {
        return $this->totalSimpananPokok() + $this->totalSimpananWajib() + $this->totalSimpananSukarela();
    }

    public function activeLoan(): ?Loan
    {
        return $this->loans()
            ->whereIn('status', ['berjalan', 'disetujui'])
            ->latest()
            ->first();
    }

    public function sisaHutangPinjaman(): float
    {
        return (float) $this->loans()
            ->whereIn('status', ['berjalan', 'disetujui'])
            ->sum('sisa_pinjaman');
    }

    public function maxPlafonPinjaman(): float
    {
        if ($this->position && (float) $this->position->plafon_pinjaman > 0) {
            return (float) $this->position->plafon_pinjaman;
        }

        return (float) Setting::get('default_plafon_pinjaman', 10000000);
    }
}
