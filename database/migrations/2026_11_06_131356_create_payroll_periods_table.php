<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('kode_periode')->unique();
            $table->string('nama_periode');
            $table->date('tanggal_cutoff');
            $table->enum('status', ['draft', 'final', 'selesai'])->default('draft');
            $table->decimal('total_simpanan_wajib', 15, 2)->default(0);
            $table->decimal('total_angsuran_pinjaman', 15, 2)->default(0);
            $table->decimal('total_pemotongan', 15, 2)->default(0);
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
