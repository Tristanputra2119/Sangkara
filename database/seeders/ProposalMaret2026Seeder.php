<?php

namespace Database\Seeders;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProposalMaret2026Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@email.com')->first();
        
        if (!$admin) {
            $admin = User::first();
        }

        // Delete existing Maret 2026 proposal if exists
        Proposal::where('month', 3)
            ->where('year', 2026)
            ->where('title', 'Pengajuan Kegiatan Bulan Maret 2026')
            ->delete();

        // Create proposal with 5 activities
        Proposal::create([
            'user_id' => $admin->id,
            'title' => 'Pengajuan Kegiatan Bulan Maret 2026',
            'month' => 3,
            'year' => 2026,
            'status' => 'approved',
            'content' => [
                [
                    'name' => 'Ngabuburit IT: Monthly Meetup GDG Bali',
                    'date' => '2026-03-07',
                    'location' => 'Ruang 01 Lantai 3, Primakara University',
                    'description' => 'Bincang teknologi hasil kolaborasi GDG Bali dan PrimDev untuk membahas inovasi Backend Development. Narasumber: Cahyadi Krishna (Backend Developer di MemoryLane) dan Akhmad Rizki (Backend Developer di Juicebox Indonesia). Waktu: Pukul 13.30 WITA - selesai. Tujuan: Membagikan wawasan praktis teknologi sisi server dan memperkuat ekosistem pengembang di Bali.',
                ],
                [
                    'name' => 'Intermediate Class (Front End): Vue.js Essentials',
                    'date' => '2026-03-07',
                    'location' => 'Ruang 01 Lantai 3, Primakara University',
                    'description' => 'Kelas rutin mingguan (total 10 pertemuan) untuk pendalaman materi antarmuka web. Jadwal: Sabtu (28 Februari, 7, 14, dan 28 Maret 2026), berlanjut hingga Mei 2026. Waktu: 10.00-12.00 WITA. Instruktur: Putu Widya Rusmananda Yasa (Front End Engineer dari pengurus PrimDev). Materi: Fundamental Vue.js, Tailwind CSS, Git & GitHub, Fetch API (Axios), dan deployment.',
                ],
                [
                    'name' => 'Intermediate Class (Back End): Server-Side Development with Express.js',
                    'date' => '2026-03-07',
                    'location' => 'Ruang 02 Lantai 3, Primakara University',
                    'description' => 'Program pelatihan rutin membangun arsitektur server (total 10 pertemuan). Jadwal: Sabtu (28 Februari, 7, 14, dan 28 Maret 2026), berlanjut hingga Mei 2026. Waktu: 10.00-12.00 WITA. Instruktur: Ida Putu Sucita Danuartha (Backend Developer dari pengurus PrimDev). Materi: Express.js, arsitektur MVC, database PostgreSQL (via Supabase), pengolahan media Cloudinary, serta Git/GitHub.',
                ],
                [
                    'name' => 'PrimDev Goes to School: Smart Learning with AI',
                    'date' => '2026-03-02',
                    'location' => 'Ruang Lab RPL SMK Negeri 1 Denpasar',
                    'description' => 'Kegiatan berbagi pengetahuan mengenai implementasi Artificial Intelligence (AI) dalam mendukung proses belajar siswa. Waktu: Senin, 2 Maret 2026 pukul 10.00 WITA. Materi: Pemanfaatan teknologi AI secara bijak untuk meningkatkan produktivitas belajar.',
                ],
                [
                    'name' => 'Advance Class Cybersecurity',
                    'date' => '2026-02-21',
                    'location' => 'Ruang 01 dan 02 Lantai 3, Primakara University',
                    'description' => 'Program pelatihan tingkat lanjut (6 sesi intensif) untuk mendalami keamanan siber. Jadwal: Mulai Sabtu, 21 Februari 2026, berlanjut pada 28 Maret hingga April 2026. Waktu: 13.30 WITA - selesai. Instruktur: Andika Wiratana (Software Engineer ahli keamanan TI). Materi: Web Security Foundation, Authentication & Authorization, celah Injection, Cross-Site Vulnerabilities, dan CVE. Metode: Simulasi menggunakan alat seperti Burp Suite dan praktik Secure Code Review.',
                ],
            ],
        ]);

        $this->command->info('✅ Proposal Maret 2026 dengan 5 kegiatan berhasil dibuat!');
    }
}
