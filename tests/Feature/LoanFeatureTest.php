<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_flat_loan_calculation_is_accurate(): void
    {
        // Pinjaman 10.000.000, 10 bulan, bunga 1% flat per bulan
        $calc = Loan::calculateFlat(10000000, 10, 1.0);

        $this->assertEquals(1000000, $calc['angsuran_pokok_perbulan']);
        $this->assertEquals(100000, $calc['angsuran_bunga_perbulan']);
        $this->assertEquals(1000000, $calc['total_bunga']);
        $this->assertEquals(11000000, $calc['total_pinjaman']);
        $this->assertEquals(1100000, $calc['total_angsuran_perbulan']);
    }

    public function test_member_can_apply_for_loan_within_position_ceiling(): void
    {
        $userSiti = User::where('username', 'siti')->first();
        $memberSiti = $userSiti->member;

        // Siti adalah staf dengan plafon 10.000.000
        $response = $this->actingAs($userSiti)->post(route('member.loan.store'), [
            'nominal_pengajuan' => 5000000,
            'tenor_bulan' => 10,
            'keperluan' => 'Kebutuhan mendesak keluarga',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('loans', [
            'member_id' => $memberSiti->id,
            'nominal_pengajuan' => 5000000,
            'tenor_bulan' => 10,
            'status' => 'pending',
        ]);
    }

    public function test_member_cannot_apply_beyond_position_ceiling(): void
    {
        $userSiti = User::where('username', 'siti')->first();

        // Siti mencoba pinjam 15.000.000 (melebihi plafon Staf 10.000.000)
        $response = $this->actingAs($userSiti)->post(route('member.loan.store'), [
            'nominal_pengajuan' => 15000000,
            'tenor_bulan' => 10,
            'keperluan' => 'Mencoba melebihi plafon',
        ]);

        $response->assertSessionHasErrors('nominal_pengajuan');
    }

    public function test_staff_can_approve_and_revise_loan(): void
    {
        $userPengurus = User::where('username', 'pengurus')->first();
        $userSiti = User::where('username', 'siti')->first();
        $memberSiti = $userSiti->member;

        $loan = Loan::create([
            'no_pinjaman' => 'PINJ-TEST-0001',
            'member_id' => $memberSiti->id,
            'nominal_pengajuan' => 8000000,
            'nominal_disetujui' => 8000000,
            'tenor_bulan' => 10,
            'bunga_persen' => 1.0,
            'status' => 'pending',
        ]);

        // Pengurus menyetujui tapi merevisi nominal menjadi 6.000.000
        $response = $this->actingAs($userPengurus)->post(route('staff.loans.approve', $loan), [
            'nominal_disetujui' => 6000000,
            'catatan_pengurus' => 'Disetujui sebagian sesuai batas rasio cicilan',
        ]);

        $response->assertSessionHasNoErrors();
        $loan->refresh();

        $this->assertEquals('disetujui', $loan->status);
        $this->assertEquals(6000000, (float) $loan->nominal_disetujui);
    }

    public function test_staff_can_disburse_loan_and_generate_installments(): void
    {
        $userPengurus = User::where('username', 'pengurus')->first();
        $userSiti = User::where('username', 'siti')->first();
        $memberSiti = $userSiti->member;

        $calc = Loan::calculateFlat(6000000, 6, 1.0);
        $loan = Loan::create([
            'no_pinjaman' => 'PINJ-TEST-0002',
            'member_id' => $memberSiti->id,
            'nominal_pengajuan' => 6000000,
            'nominal_disetujui' => 6000000,
            'tenor_bulan' => 6,
            'bunga_persen' => 1.0,
            'total_bunga' => $calc['total_bunga'],
            'total_pinjaman' => $calc['total_pinjaman'],
            'angsuran_pokok_perbulan' => $calc['angsuran_pokok_perbulan'],
            'angsuran_bunga_perbulan' => $calc['angsuran_bunga_perbulan'],
            'total_angsuran_perbulan' => $calc['total_angsuran_perbulan'],
            'sisa_pinjaman' => $calc['total_pinjaman'],
            'sisa_tenor' => 6,
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($userPengurus)->post(route('staff.loans.disburse', $loan));

        $response->assertSessionHasNoErrors();
        $loan->refresh();

        $this->assertEquals('berjalan', $loan->status);
        $this->assertCount(6, $loan->installments);
    }
}
