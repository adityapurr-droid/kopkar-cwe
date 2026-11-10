<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffLoanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Loan::with(['member.division', 'member.position', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pinjaman', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($mq) use ($search) {
                        $mq->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nik_karyawan', 'like', "%{$search}%");
                    });
            });
        }

        $loans = $query->latest()->paginate(15)->withQueryString();
        $counts = [
            'all' => Loan::count(),
            'pending' => Loan::where('status', 'pending')->count(),
            'disetujui' => Loan::where('status', 'disetujui')->count(),
            'berjalan' => Loan::where('status', 'berjalan')->count(),
            'lunas' => Loan::where('status', 'lunas')->count(),
            'ditolak' => Loan::where('status', 'ditolak')->count(),
        ];

        return view('staff.loans.index', compact('loans', 'counts'));
    }

    public function show(Loan $loan): View
    {
        $loan->load(['member.division', 'member.position', 'approver', 'installments']);
        $member = $loan->member;
        $totalSimpanan = $member->totalSaldoSimpanan();
        $maxPlafon = $member->maxPlafonPinjaman();

        return view('staff.loans.show', compact('loan', 'member', 'totalSimpanan', 'maxPlafon'));
    }

    public function approve(Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Pinjaman ini sudah tidak dalam status pending.');
        }

        $maxPlafon = $loan->member->maxPlafonPinjaman();

        $request->validate([
            'nominal_disetujui' => ['required', 'numeric', 'min:500000', "max:{$maxPlafon}"],
            'catatan_pengurus' => ['nullable', 'string', 'max:500'],
        ]);

        $nominalDisetujui = (float) $request->nominal_disetujui;
        $calc = Loan::calculateFlat($nominalDisetujui, $loan->tenor_bulan, (float) $loan->bunga_persen);

        $sebelum = $loan->toArray();

        $loan->update([
            'nominal_disetujui' => $nominalDisetujui,
            'total_bunga' => $calc['total_bunga'],
            'total_pinjaman' => $calc['total_pinjaman'],
            'angsuran_pokok_perbulan' => $calc['angsuran_pokok_perbulan'],
            'angsuran_bunga_perbulan' => $calc['angsuran_bunga_perbulan'],
            'total_angsuran_perbulan' => $calc['total_angsuran_perbulan'],
            'sisa_pinjaman' => $calc['total_pinjaman'],
            'sisa_tenor' => $loan->tenor_bulan,
            'status' => 'disetujui',
            'catatan_pengurus' => $request->catatan_pengurus ?: 'Disetujui oleh pengurus.',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLog::record(
            'Pinjaman',
            'APPROVE',
            "Persetujuan pinjaman {$loan->no_pinjaman} sebesar Rp ".number_format($nominalDisetujui, 0, ',', '.'),
            $sebelum,
            $loan->toArray()
        );

        return back()->with('success', 'Permohonan pinjaman berhasil disetujui. Silakan lakukan pencairan dana jika dana sudah diserahkan ke anggota.');
    }

    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Pinjaman ini sudah tidak dalam status pending.');
        }

        $request->validate([
            'catatan_pengurus' => ['required', 'string', 'max:500'],
        ], [
            'catatan_pengurus.required' => 'Mohon sertakan alasan penolakan pinjaman.',
        ]);

        $sebelum = $loan->toArray();

        $loan->update([
            'status' => 'ditolak',
            'catatan_pengurus' => $request->catatan_pengurus,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLog::record(
            'Pinjaman',
            'REJECT',
            "Penolakan permohonan pinjaman {$loan->no_pinjaman}. Alasan: {$request->catatan_pengurus}",
            $sebelum,
            $loan->toArray()
        );

        return back()->with('success', 'Permohonan pinjaman telah ditolak.');
    }

    public function disburse(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'disetujui') {
            return back()->with('error', 'Hanya pinjaman berstatus disetujui yang dapat dicairkan.');
        }

        DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => 'berjalan',
                'disbursed_at' => now(),
            ]);

            // Buat jadwal angsuran mulai bulan berikutnya
            $loan->generateInstallments(now());

            AuditLog::record(
                'Pinjaman',
                'DISBURSE',
                "Pencairan dana pinjaman {$loan->no_pinjaman} sebesar Rp ".number_format((float) $loan->nominal_disetujui, 0, ',', '.').' berhasil diproses.',
                null,
                $loan->toArray()
            );
        });

        return back()->with('success', 'Dana pinjaman berhasil dicairkan! Jadwal angsuran bulanan telah diterbitkan.');
    }
}
