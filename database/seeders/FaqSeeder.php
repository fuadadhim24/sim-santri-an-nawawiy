<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // Kategori: Pendaftaran
        // ──────────────────────────────────────────────
        Faq::create([
            'title' => 'Bagaimana cara mendaftarkan santri baru?',
            'content' => "Pendaftaran santri baru dilakukan melalui sistem SPMB (Seleksi Penerimaan Murid Baru) yang tersedia di dashboard wali santri.\n\nLangkah-langkah:\n1. Login ke akun wali santri\n2. Klik menu \"Jadwal SPMB\" atau \"Lihat Jadwal Pendaftaran\"\n3. Pilih periode SPMB yang sedang aktif\n4. Isi formulir pendaftaran santri baru\n5. Upload dokumen yang diperlukan\n6. Tunggu verifikasi dan konfirmasi dari admin",
            'category' => 'pendaftaran',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Faq::create([
            'title' => 'Dokumen apa saja yang perlu disiapkan?',
            'content' => "Dokumen yang perlu disiapkan untuk pendaftaran santri baru:\n\n• Kartu Keluarga (KK)\n• Pas Foto Terbaru\n• NISN (oleh Operator)\n• Akta Kelahiran\n• Ijazah\n\nSemua dokumen diupload dalam format JPG/PNG/PDF dengan ukuran maksimal 2MB per file.",
            'category' => 'pendaftaran',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // ──────────────────────────────────────────────
        // Kategori: Program
        // ──────────────────────────────────────────────
        Faq::create([
            'title' => 'Jenjang pendidikan apa saja yang tersedia?',
            'content' => "Pesantren An-Nawawiy menyediakan 3 jenjang pendidikan:\n\n1. SMP (Sekolah Menengah Pertama)\n   - Kelas 7, 8, dan 9\n   - Kurikulum Merdeka + kurikulum pesantren\n\n2. SMA (Sekolah Menengah Atas)\n   - Kelas 10, 11, dan 12\n   - Kurikulum Merdeka + kurikulum pesantren\n\n3. PPTQ (Program Pendidikan Tahfizh Al-Qur'an)\n   - Program hafalan Al-Qur'an intensif\n   - Target 30 Juz dalam 3 tahun",
            'category' => 'program',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Faq::create([
            'title' => 'Apa perbedaan status domisili Mondok dan Non-Mondok?',
            'content' => "Status domisili santri terbagi menjadi:\n\n• MONDOK: Santri tinggal di asrama pesantren. Mendapat fasilitas tempat tinggal, makan 3x sehari, dan bimbingan 24 jam.\n\n• NON-MONDOK: Santri pulang ke rumah setelah jam pelajaran sekolah selesai.\n\n• NGAJI ONLY: Santri hanya mengikuti kegiatan pengajian/madrasah diniyah tanpa mengikuti sekolah formal.\n\nBiaya SPP dan fasilitas berbeda sesuai status domisili masing-masing.",
            'category' => 'program',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // ──────────────────────────────────────────────
        // Kategori: Biaya
        // ──────────────────────────────────────────────
        Faq::create([
            'title' => 'Rincian biaya pendidikan',
            'content' => "Biaya pendidikan di Pesantren An-Nawawiy terdiri dari:\n\n1. Biaya Pendaftaran SPMB: Rp 10.000 (satu kali)\n2. Biaya Daftar Ulang Semester: Rp 15.000 (per semester)\n3. SPP Bulanan:\n   - SMP: Rp 10.000/bulan\n   - SMA: Rp 12.000/bulan\n   - PPTQ: Rp 11.000/bulan\n4. Biaya Asrama (khusus mondok): Rp 13.000/bulan\n\nTerdapat potongan khusus untuk:\n• Anak Guru/Karyawan\n• Santri Yatim/Yatim Piatu\n\nUntuk informasi lebih detail, hubungi admin pesantren.",
            'category' => 'biaya',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Faq::create([
            'title' => 'Metode pembayaran yang tersedia',
            'content' => "Pembayaran SPP dan biaya lainnya dapat dilakukan melalui:\n\n1. Cash — Bayar langsung di kantor TU pesantren\n2. Transfer Online (Duitku) — Pembayaran otomatis via berbagai channel:\n   - Virtual Account (BCA, Mandiri, BNI, BRI, dll)\n   - E-Wallet (GoPay, OVO, DANA, dll)\n   - Minimarket (Alfamart, Indomaret)\n\nPembayaran online dapat dilakukan langsung dari dashboard wali santri dengan klik tombol \"Bayar\" pada tagihan yang aktif.",
            'category' => 'biaya',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // ──────────────────────────────────────────────
        // Kategori: Fasilitas
        // ──────────────────────────────────────────────
        Faq::create([
            'title' => 'Fasilitas pesantren',
            'content' => "Fasilitas yang tersedia di Pesantren An-Nawawiy:\n\n• Asrama putra dan putri terpisah\n• Masjid / Musholla\n• Ruang kelas berpendingin\n• Laboratorium komputer\n• Perpustakaan\n• Lapangan olahraga\n• Kantin sehat\n• Klinik kesehatan\n• Wi-Fi untuk kegiatan belajar\n• CCTV keamanan 24 jam",
            'category' => 'fasilitas',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // ──────────────────────────────────────────────
        // Kategori: Umum
        // ──────────────────────────────────────────────
        Faq::create([
            'title' => 'Jam berkunjung wali santri',
            'content' => "Jadwal kunjungan wali santri:\n\n• Hari biasa: Sabtu & Minggu pukul 09.00 - 15.00 WIB\n• Hari libur nasional: 09.00 - 16.00 WIB\n\nKetentuan:\n1. Wajib lapor ke pos keamanan\n2. Tidak diperkenankan membawa makanan dari luar tanpa izin\n3. Kunjungan di luar jadwal harus koordinasi dengan wali kelas",
            'category' => 'umum',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Faq::create([
            'title' => 'Kontak darurat pesantren',
            'content' => "Hubungi kami melalui:\n\n• Kantor Admin: +62 812-3456-7890\n• WhatsApp Humas: +62 813-9876-5432\n• Email: admin@an-nawawiy.sch.id\n• Alamat: Jl. Pesantren No. 123, Kota\n\nUntuk keadaan darurat di luar jam kerja, hubungi langsung WhatsApp Humas.",
            'category' => 'umum',
            'sort_order' => 2,
            'is_active' => true,
        ]);
    }
}
