# Panduan Modul Sistem Informasi Koperasi Karyawan POLTEK CWE

Proyek ini telah distrukturisasi ke dalam modul-modul fungsional yang terpisah agar memudahkan Anda dalam membaca, memodifikasi, dan mengembangkan kode program secara mandiri.

---

## 1. Peta Modul Rute (`routes/modules/`)

File `routes/web.php` hanya bertindak sebagai pemanggil (aggregator). Seluruh rute dikelompokkan berdasarkan fungsinya:

| Modul Rute | Lokasi File | Fungsi & Isi Endpoint |
| :--- | :--- | :--- |
| **Autentikasi** | `routes/modules/auth.php` | Halaman login, proses login, dan logout. |
| **Portal Anggota** | `routes/modules/member.php` | Dashboard saldo, kalkulator simulasi, form pengajuan pinjaman, riwayat, dan unduh slip potongan gaji PDF. |
| **Operasional Pengurus** | `routes/modules/staff.php` | Manajemen anggota, input simpanan, review/approval/pencairan pinjaman, generator payroll CWE, dan ekspor laporan. |
| **Admin Sistem** | `routes/modules/admin.php` | Kelola user, reset password, master divisi & plafon pinjaman jabatan, konfigurasi suku bunga, dan audit log. |

> **Tips:** Jika Anda ingin menambah atau mengubah URL halaman tertentu, cukup buka file rute modul terkait di atas.

---

## 2. Modul Layanan Logika Bisnis (`app/Services/`)

Rumus dan alur transaksi utama dipisahkan dari controller agar tidak berantakan:

- **`app/Services/LoanService.php`**
  - Mengatur perhitungan **Bunga Flat** per bulan.
  - Mengatur penerbitan tabel amortisasi jadwal angsuran (`loan_installments`).
  - Mengatur validasi batas plafon pinjaman berdasarkan jabatan anggota.
- **`app/Services/PayrollService.php`**
  - Mengatur kalkulasi otomatis pemotongan bulanan (Simpanan Wajib Rp 50.000 + Angsuran Pinjaman).
  - Mengatur sinkronisasi pelunasan ke saldo anggota saat payroll diselesaikan.

---

## 3. Peta Controller (`app/Http/Controllers/`)

| Modul | Controller | Deskripsi Tugas |
| :--- | :--- | :--- |
| **Auth** | `AuthController.php` | Penanganan otentikasi dan pengalihan dashboard sesuai role. |
| **Member** | `MemberPortalController.php` | Logika tampilan portal anggota, simulasi pinjaman, dan riwayat. |
| **Staff Operasional** | `StaffDashboardController.php` | Statistik ringkasan kas, simpanan, dan antrean approval. |
| | `MemberManagementController.php` | Pendaftaran anggota baru, edit profil, dan aktivasi akun. |
| | `StaffSavingController.php` | Input simpanan pokok, wajib, dan sukarela (setor/tarik). |
| | `StaffLoanController.php` | Approval pinjaman (bisa revisi nominal), tolak, dan pencairan dana. |
| | `PayrollController.php` | Generate payroll bulanan, ekspor Excel/PDF, dan pelunasan. |
| | `ReportController.php` | Rekapitulasi simpanan, pinjaman aktif, dan arus kas. |
| **Admin** | `UserController.php` | Manajemen akun dan reset password. |
| | `MasterDataController.php` | Kelola Divisi & Jabatan (serta batas plafon masing-masing). |
| | `SettingController.php` | Konfigurasi bunga %, simpanan pokok/wajib, tenor maksimal. |
| | `AuditLogController.php` | Penelusuran rekam jejak aktivitas user dan transaksi. |

---

## 4. Peta Tampilan Antarmuka (`resources/views/`)

Tampilan Blade disusun mengikuti struktur modul:

- **Layout Utama**:
  - `resources/views/layouts/staff.blade.php`: Sidebar back-office pengurus & admin.
  - `resources/views/layouts/member.blade.php`: Portal anggota ramah mobile (bottom bar navigasi).
- **Portal Anggota**:
  - `resources/views/member/dashboard.blade.php`: Tampilan kartu saldo & status pinjaman.
  - `resources/views/member/simulation.blade.php`: Kalkulator simulasi Alpine.js & form pinjaman.
  - `resources/views/member/loan-detail.blade.php`: Rincian jadwal cicilan pinjaman.
  - `resources/views/member/history.blade.php`: Tab mutasi simpanan, potongan payroll, dan pinjaman.
- **Pengurus & Keuangan**:
  - `resources/views/staff/dashboard.blade.php`
  - `resources/views/staff/members/` (index, create, show)
  - `resources/views/staff/savings/` (index & modal setor/tarik)
  - `resources/views/staff/loans/` (index & review approval show)
  - `resources/views/staff/payroll/` (index periode & matriks potongan gaji)
  - `resources/views/staff/reports/` (laporan keuangan simpan pinjam)
- **Admin**:
  - `resources/views/admin/users/` (manajemen user & reset sandi)
  - `resources/views/admin/master/` (divisi & plafon jabatan)
  - `resources/views/admin/settings/` (konfigurasi koperasi)
  - `resources/views/admin/audit-logs/` (log aktivitas)
- **Ekspor Dokumen (PDF & Excel)**:
  - `resources/views/exports/payroll-pdf.blade.php`: Cetak rekap potongan gaji payroll.
  - `resources/views/exports/member-slip-pdf.blade.php`: Cetak slip potongan gaji individu anggota.
  - `resources/views/exports/savings-pdf.blade.php`: Cetak rekap saldo simpanan anggota.
  - `resources/views/exports/loans-pdf.blade.php`: Cetak daftar pinjaman aktif berjalan.
  - `app/Exports/`: Berkas generator Microsoft Excel (.xlsx).

