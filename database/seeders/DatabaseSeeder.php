<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Position;
use App\Models\Saving;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Settings
        $settings = [
            [
                'key' => 'nama_koperasi',
                'value' => 'Koperasi Karyawan Politeknik Kelapa Sawit Citra Widya Edukasi',
                'label' => 'Nama Koperasi',
                'tipe' => 'text',
                'deskripsi' => 'Nama resmi organisasi koperasi',
            ],
            [
                'key' => 'singkatan_koperasi',
                'value' => 'KOPKAR POLTEK CWE',
                'label' => 'Singkatan / Kode Koperasi',
                'tipe' => 'text',
                'deskripsi' => 'Singkatan yang ditampilkan di header dan dokumen',
            ],
            [
                'key' => 'simpanan_pokok_nominal',
                'value' => '100000',
                'label' => 'Simpanan Pokok (Rp)',
                'tipe' => 'currency',
                'deskripsi' => 'Nominal simpanan pokok saat pertama kali menjadi anggota (sekali bayar)',
            ],
            [
                'key' => 'simpanan_wajib_nominal',
                'value' => '50000',
                'label' => 'Simpanan Wajib Bulanan (Rp)',
                'tipe' => 'currency',
                'deskripsi' => 'Nominal simpanan wajib rutin yang dipotong dari payroll setiap bulan',
            ],
            [
                'key' => 'bunga_pinjaman_persen',
                'value' => '1.00',
                'label' => 'Suku Bunga Pinjaman (% / bulan flat)',
                'tipe' => 'percent',
                'deskripsi' => 'Persentase bunga flat per bulan untuk pinjaman reguler',
            ],
            [
                'key' => 'tenor_maksimal_bulan',
                'value' => '24',
                'label' => 'Tenor Maksimal Pinjaman (Bulan)',
                'tipe' => 'number',
                'deskripsi' => 'Batas maksimal jangka waktu angsuran pinjaman dalam bulan',
            ],
            [
                'key' => 'default_plafon_pinjaman',
                'value' => '10000000',
                'label' => 'Plafon Pinjaman Default (Rp)',
                'tipe' => 'currency',
                'deskripsi' => 'Plafon standar jika jabatan tidak menentukan plafon khusus',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 2. Divisions
        $divAkademik = Division::create([
            'nama' => 'Divisi Akademik & Pengajaran',
            'kode' => 'AKD',
            'deskripsi' => 'Pengelolaan kurikulum, perkuliahan, dan pengajaran kelapa sawit',
        ]);
        $divRiset = Division::create([
            'nama' => 'Divisi Riset & Kebun Praktik',
            'kode' => 'RKP',
            'deskripsi' => 'Penelitian kelapa sawit dan operasional kebun praktik',
        ]);
        $divKeuangan = Division::create([
            'nama' => 'Divisi Keuangan & Administrasi Umum',
            'kode' => 'KAU',
            'deskripsi' => 'Administrasi umum, SDM, dan keuangan POLTEK CWE',
        ]);
        $divSarpras = Division::create([
            'nama' => 'Divisi Sarana & Prasarana Kampus',
            'kode' => 'SPK',
            'deskripsi' => 'Pemeliharaan fasilitas laboratorium, bengkel, dan kampus',
        ]);

        // 3. Positions with Plafons
        $posDirektur = Position::create([
            'nama' => 'Direktur / Wakil Direktur',
            'plafon_pinjaman' => 30000000,
            'deskripsi' => 'Pimpinan Politeknik CWE',
        ]);
        $posKaprodi = Position::create([
            'nama' => 'Kepala Program Studi (Kaprodi)',
            'plafon_pinjaman' => 20000000,
            'deskripsi' => 'Ketua program studi vokasi perkebunan',
        ]);
        $posDosen = Position::create([
            'nama' => 'Dosen Tetap',
            'plafon_pinjaman' => 15000000,
            'deskripsi' => 'Dosen fungsional akademik',
        ]);
        $posStaf = Position::create([
            'nama' => 'Staf Administrasi / Laboran',
            'plafon_pinjaman' => 10000000,
            'deskripsi' => 'Tenaga kependidikan dan teknisi laboratorium',
        ]);
        $posTeknisi = Position::create([
            'nama' => 'Teknisi / Operator Lapangan',
            'plafon_pinjaman' => 7500000,
            'deskripsi' => 'Tenaga teknis lapangan kebun praktik',
        ]);

        // 4. Default Users
        $admin = User::create([
            'name' => 'Administrator Koperasi',
            'username' => 'admin',
            'email' => 'admin@poltekcwe.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $pengurus = User::create([
            'name' => 'Bendahara & Pengurus Koperasi',
            'username' => 'pengurus',
            'email' => 'keuangan@poltekcwe.ac.id',
            'password' => Hash::make('password'),
            'role' => 'pengurus',
            'is_active' => true,
        ]);

        // 5. Members & User Anggota
        // Anggota 1
        $userBudi = User::create([
            'name' => 'Ir. Budi Santoso, M.T.',
            'username' => 'budi',
            'email' => 'budi.santoso@poltekcwe.ac.id',
            'password' => Hash::make('password'),
            'role' => 'anggota',
            'is_active' => true,
        ]);

        $memberBudi = Member::create([
            'user_id' => $userBudi->id,
            'nik_karyawan' => 'CWE-2018-042',
            'no_anggota' => 'KOP-001',
            'nama_lengkap' => 'Ir. Budi Santoso, M.T.',
            'division_id' => $divAkademik->id,
            'position_id' => $posDosen->id,
            'no_hp' => '081234567890',
            'alamat' => 'Komplek Perumahan Dosen POLTEK CWE, Bekasi',
            'tanggal_bergabung' => '2023-01-15',
            'status_keanggotaan' => 'aktif',
        ]);

        // Simpanan Budi
        Saving::create([
            'member_id' => $memberBudi->id,
            'jenis_simpanan' => 'pokok',
            'tipe' => 'masuk',
            'nominal' => 100000,
            'tanggal_transaksi' => '2023-01-15',
            'metode_bayar' => 'tunai',
            'keterangan' => 'Simpanan Pokok Awal',
            'created_by' => $pengurus->id,
        ]);
        Saving::create([
            'member_id' => $memberBudi->id,
            'jenis_simpanan' => 'wajib',
            'tipe' => 'masuk',
            'nominal' => 600000, // 12 bulan x 50.000
            'tanggal_transaksi' => '2026-10-25',
            'metode_bayar' => 'potong_gaji',
            'keterangan' => 'Akumulasi Simpanan Wajib s.d Oktober 2026',
            'created_by' => $pengurus->id,
        ]);
        Saving::create([
            'member_id' => $memberBudi->id,
            'jenis_simpanan' => 'sukarela',
            'tipe' => 'masuk',
            'nominal' => 1500000,
            'tanggal_transaksi' => '2026-06-10',
            'metode_bayar' => 'transfer',
            'keterangan' => 'Simpanan Sukarela Tambahan',
            'created_by' => $pengurus->id,
        ]);

        // Anggota 2
        $userSiti = User::create([
            'name' => 'Siti Aminah, S.Kom.',
            'username' => 'siti',
            'email' => 'siti.aminah@poltekcwe.ac.id',
            'password' => Hash::make('password'),
            'role' => 'anggota',
            'is_active' => true,
        ]);

        $memberSiti = Member::create([
            'user_id' => $userSiti->id,
            'nik_karyawan' => 'CWE-2021-098',
            'no_anggota' => 'KOP-002',
            'nama_lengkap' => 'Siti Aminah, S.Kom.',
            'division_id' => $divKeuangan->id,
            'position_id' => $posStaf->id,
            'no_hp' => '082198765432',
            'alamat' => 'Cikarang Barat, Kab. Bekasi',
            'tanggal_bergabung' => '2024-03-01',
            'status_keanggotaan' => 'aktif',
        ]);

        Saving::create([
            'member_id' => $memberSiti->id,
            'jenis_simpanan' => 'pokok',
            'tipe' => 'masuk',
            'nominal' => 100000,
            'tanggal_transaksi' => '2024-03-01',
            'metode_bayar' => 'tunai',
            'keterangan' => 'Simpanan Pokok Awal',
            'created_by' => $pengurus->id,
        ]);
        Saving::create([
            'member_id' => $memberSiti->id,
            'jenis_simpanan' => 'wajib',
            'tipe' => 'masuk',
            'nominal' => 400000,
            'tanggal_transaksi' => '2026-10-25',
            'metode_bayar' => 'potong_gaji',
            'keterangan' => 'Akumulasi Simpanan Wajib s.d Oktober 2026',
            'created_by' => $pengurus->id,
        ]);

        // Contoh Pinjaman Aktif untuk Budi
        $calc = Loan::calculateFlat(6000000, 10, 1.00);
        $loanBudi = Loan::create([
            'no_pinjaman' => 'PINJ-202607-0001',
            'member_id' => $memberBudi->id,
            'nominal_pengajuan' => 6000000,
            'nominal_disetujui' => 6000000,
            'tenor_bulan' => 10,
            'bunga_persen' => 1.00,
            'total_bunga' => $calc['total_bunga'],
            'total_pinjaman' => $calc['total_pinjaman'],
            'angsuran_pokok_perbulan' => $calc['angsuran_pokok_perbulan'],
            'angsuran_bunga_perbulan' => $calc['angsuran_bunga_perbulan'],
            'total_angsuran_perbulan' => $calc['total_angsuran_perbulan'],
            'sisa_pinjaman' => $calc['total_pinjaman'] - ($calc['total_angsuran_perbulan'] * 3), // Anggap sudah bayar 3 bulan
            'sisa_tenor' => 7,
            'keperluan' => 'Renovasi Rumah & Biaya Pendidikan',
            'status' => 'berjalan',
            'catatan_pengurus' => 'Disetujui penuh sesuai rekomendasi plafon jabatan Dosen',
            'approved_by' => $pengurus->id,
            'approved_at' => Carbon::now()->subMonths(3),
            'disbursed_at' => Carbon::now()->subMonths(3),
        ]);

        $loanBudi->generateInstallments(Carbon::now()->subMonths(3));

        // Tandai 3 angsuran pertama sebagai lunas
        $installments = $loanBudi->installments()->orderBy('angsuran_ke')->take(3)->get();
        foreach ($installments as $inst) {
            $inst->update([
                'status_bayar' => 'lunas',
                'tanggal_bayar' => $inst->jatuh_tempo,
            ]);
        }
    }
}
