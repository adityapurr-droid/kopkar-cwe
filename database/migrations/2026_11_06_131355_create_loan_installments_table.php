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
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->integer('angsuran_ke');
            $table->decimal('nominal_pokok', 15, 2);
            $table->decimal('nominal_bunga', 15, 2);
            $table->decimal('total_angsuran', 15, 2);
            $table->decimal('sisa_saldo_pokok', 15, 2)->default(0);
            $table->date('jatuh_tempo');
            $table->enum('status_bayar', ['belum', 'lunas'])->default('belum');
            $table->timestamp('tanggal_bayar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_installments');
    }
};
