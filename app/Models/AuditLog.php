<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'aksi',
        'modul',
        'deskripsi',
        'payload_sebelum',
        'payload_sesudah',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'payload_sebelum' => 'array',
            'payload_sesudah' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $modul,
        string $aksi,
        string $deskripsi,
        ?array $sebelum = null,
        ?array $sesudah = null
    ): static {
        return static::create([
            'user_id' => Auth::id(),
            'modul' => $modul,
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'payload_sebelum' => $sebelum,
            'payload_sesudah' => $sesudah,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
        ]);
    }
}
