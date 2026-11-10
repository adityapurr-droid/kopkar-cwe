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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('jenis_simpanan', ['pokok', 'wajib', 'sukarela']);
            $table->enum('tipe', ['masuk', 'keluar'])->default('masuk');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_transaksi');
            $table->enum('metode_bayar', ['potong_gaji', 'tunai', 'transfer'])->default('potong_gaji');
            $table->string('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
