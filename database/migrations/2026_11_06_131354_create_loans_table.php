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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('no_pinjaman')->unique();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->decimal('nominal_pengajuan', 15, 2);
            $table->decimal('nominal_disetujui', 15, 2)->default(0);
            $table->integer('tenor_bulan');
            $table->decimal('bunga_persen', 5, 2)->default(1.00);
            $table->decimal('total_bunga', 15, 2)->default(0);
            $table->decimal('total_pinjaman', 15, 2)->default(0);
            $table->decimal('angsuran_pokok_perbulan', 15, 2)->default(0);
            $table->decimal('angsuran_bunga_perbulan', 15, 2)->default(0);
            $table->decimal('total_angsuran_perbulan', 15, 2)->default(0);
            $table->decimal('sisa_pinjaman', 15, 2)->default(0);
            $table->integer('sisa_tenor')->default(0);
            $table->text('keperluan')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'berjalan', 'lunas', 'dibatalkan'])->default('pending');
            $table->text('catatan_pengurus')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
