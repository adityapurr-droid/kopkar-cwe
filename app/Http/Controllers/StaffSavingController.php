<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Saving;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffSavingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Saving::with(['member.division', 'creator']);

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('jenis_simpanan')) {
            $query->where('jenis_simpanan', $request->jenis_simpanan);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        $savings = $query->latest('tanggal_transaksi')->paginate(15)->withQueryString();
        $members = Member::where('status_keanggotaan', 'aktif')->orderBy('nama_lengkap')->get();

        $totalMasuk = (float) Saving::where('tipe', 'masuk')->sum('nominal');
        $totalKeluar = (float) Saving::where('tipe', 'keluar')->sum('nominal');
        $saldoTotal = $totalMasuk - $totalKeluar;

        return view('staff.savings.index', compact('savings', 'members', 'saldoTotal', 'totalMasuk', 'totalKeluar'));
    }

    public function storeBatch(Request $request): RedirectResponse
    {
        $request->validate([
            'select_all' => ['nullable', 'boolean'],
            'member_id' => ['required_without:select_all', 'nullable', 'exists:members,id'],
            'member_ids' => ['nullable', 'array'],
            'jenis_simpanan' => ['required', 'in:pokok,wajib,sukarela'],
            'tipe' => ['required', 'in:masuk,keluar'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'tanggal_transaksi' => ['required', 'date'],
            'metode_bayar' => ['required', 'in:tunai,transfer,potong_gaji'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $memberIds = $request->boolean('select_all')
            ? Member::where('status_keanggotaan', 'aktif')->pluck('id')->toArray()
            : [$request->member_id];

        $nominal = (float) $request->nominal;
        $count = count($memberIds);

        DB::transaction(function () use ($request, $memberIds, $nominal) {
            foreach ($memberIds as $id) {
                $member = Member::find($id);
                if (! $member) {
                    continue;
                }

                if ($request->tipe === 'keluar' && $request->jenis_simpanan === 'sukarela') {
                    if ($nominal > $member->totalSimpananSukarela()) {
                        continue;
                    }
                }

                Saving::create([
                    'member_id' => $id,
                    'jenis_simpanan' => $request->jenis_simpanan,
                    'tipe' => $request->tipe,
                    'nominal' => $nominal,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                    'metode_bayar' => $request->metode_bayar,
                    'keterangan' => $request->keterangan ?: 'Batch '.$request->jenis_simpanan.' ('.$request->tipe.')',
                    'created_by' => auth()->id(),
                ]);
            }

            AuditLog::record(
                'Simpanan',
                'CREATE',
                "Pencatatan batch simpanan {$request->jenis_simpanan} ({$request->tipe}) untuk ".count($memberIds).' anggota. Total: Rp '.number_format($nominal * count($memberIds), 0, ',', '.')
            );
        });

        return back()->with('success', "Berhasil mencatat {$count} transaksi simpanan sekaligus.");
    }
}
