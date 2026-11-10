<?php

namespace Tests\Feature;

use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_staff_can_generate_monthly_payroll_deductions(): void
    {
        $userPengurus = User::where('username', 'pengurus')->first();

        $response = $this->actingAs($userPengurus)->post(route('staff.payroll.generate'), [
            'kode_periode' => '2026-12',
            'nama_periode' => 'Gaji Desember 2026',
            'tanggal_cutoff' => '2026-12-25',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('payroll_periods', [
            'kode_periode' => '2026-12',
            'status' => 'draft',
        ]);

        $period = PayrollPeriod::where('kode_periode', '2026-12')->first();
        // Database seeder has 2 members: Budi (with active loan 660.000 + 50.000 wajib = 710.000) and Siti (50.000 wajib)
        $this->assertCount(2, $period->deductions);
        $this->assertEquals(100000, (float) $period->total_simpanan_wajib); // 2 x 50.000
    }

    public function test_staff_can_settle_payroll_period(): void
    {
        $userPengurus = User::where('username', 'pengurus')->first();

        // 1. Generate periode
        $this->actingAs($userPengurus)->post(route('staff.payroll.generate'), [
            'kode_periode' => '2026-12',
            'nama_periode' => 'Gaji Desember 2026',
            'tanggal_cutoff' => '2026-12-25',
        ]);

        $period = PayrollPeriod::where('kode_periode', '2026-12')->first();

        // 2. Selesaikan & Sinkronkan
        $response = $this->actingAs($userPengurus)->post(route('staff.payroll.settle', $period));

        $response->assertSessionHasNoErrors();
        $period->refresh();

        $this->assertEquals('selesai', $period->status);

        // Pastikan Simpanan Wajib tercatat di tabel savings
        $this->assertDatabaseHas('savings', [
            'jenis_simpanan' => 'wajib',
            'metode_bayar' => 'potong_gaji',
            'nominal' => 50000,
        ]);
    }
}
