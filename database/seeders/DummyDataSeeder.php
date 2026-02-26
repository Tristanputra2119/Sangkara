<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Meeting;
use App\Models\Proposal;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->warn('Tidak ada user di database, pastikan sudah register atau menjalankan seeder shield.');
            return;
        }

        $this->command->info('Memulai seeding data dummy...');

        // --- 1. Kategori (Manajemen Keuangan) ---
        $catDanaKampus = Category::firstOrCreate(['name' => 'Dana Kampus'], ['type' => 'income']);
        $catSponsor    = Category::firstOrCreate(['name' => 'Sponsorship'], ['type' => 'income']);
        $catKonsumsi   = Category::firstOrCreate(['name' => 'Konsumsi'], ['type' => 'expense']);
        $catPerlengkapan = Category::firstOrCreate(['name' => 'Perlengkapan'], ['type' => 'expense']);
        $catHonor      = Category::firstOrCreate(['name' => 'Honor Pembicara'], ['type' => 'expense']);

        // --- 2. Transaksi (Manajemen Keuangan) ---
        $transactions = [
            ['category_id' => $catDanaKampus->id, 'amount' => 5000000, 'description' => 'Pencairan dana kegiatan BEM bulan Agustus', 'transaction_date' => '2026-08-01'],
            ['category_id' => $catSponsor->id, 'amount' => 2000000, 'description' => 'Sponsorship dari PT. Teknologi Maju', 'transaction_date' => '2026-08-05'],
            ['category_id' => $catPerlengkapan->id, 'amount' => -1500000, 'description' => 'Sewa sound system dan proyektor', 'transaction_date' => '2026-08-10'],
            ['category_id' => $catKonsumsi->id, 'amount' => -1000000, 'description' => 'Konsumsi panitia dan peserta seminar (150 pax)', 'transaction_date' => '2026-08-15'],
            ['category_id' => $catHonor->id, 'amount' => -3000000, 'description' => 'Honorarium untuk 3 narasumber eksternal', 'transaction_date' => '2026-08-15'],
        ];

        foreach ($transactions as $dt) {
            Transaction::updateOrCreate(
                ['description' => $dt['description']],
                [
                    'user_id' => $user->id,
                    'category_id' => $dt['category_id'],
                    'amount' => $dt['amount'],
                    'transaction_date' => $dt['transaction_date']
                ]
            );
        }

        // --- 3. Rapat (Manajemen Kegiatan) ---
        $meetings = [
            [
                'title' => 'Rapat Pembentukan Panitia STMP 2026',
                'agenda' => 'Pemilihan ketua pelaksana dan pembagian divisi kerja.',
                'minutes_content' => '<p><strong>Hasil Rapat:</strong></p><ul><li>Ketua Pelaksana: Budi Santoso</li><li>Divisi Acara: Siti Aminah</li><li>Divisi Perlengkapan: Joko Widodo</li></ul>',
                'meeting_date' => '2026-07-20 10:00:00',
            ],
            [
                'title' => 'Rapat Fixasi Acara STMP 2026',
                'agenda' => 'Finalisasi rundown dan narasumber fix.',
                'minutes_content' => '<p>Narasumber yang sudah konfirmasi: Bapak Arief (CEO Startup A), Ibu Dian (Dosen IT). Rundown acara sudah fix dari jam 09.00 - 13.00.</p>',
                'meeting_date' => '2026-08-05 14:00:00',
            ]
        ];

        foreach ($meetings as $dm) {
            Meeting::updateOrCreate(
                ['title' => $dm['title']],
                [
                    'created_by' => $user->id,
                    'agenda' => $dm['agenda'],
                    'minutes_content' => $dm['minutes_content'],
                    'meeting_date' => $dm['meeting_date']
                ]
            );
        }

        // --- 4. Pengajuan (Proposal) ---
        Proposal::updateOrCreate(
            ['title' => 'Pengajuan Kegiatan STMP Ekstra 2026 (Draft)'],
            [
                'user_id' => $user->id,
                'month'   => 9,
                'year'    => 2026,
                'status'  => 'draft',
                'content' => [
                    [
                        'name'        => 'Workshop Koding Dasar',
                        'date'        => '2026-09-10',
                        'location'    => 'Lab Komputer A',
                        'description' => '<p>Workshop pengenalan Laravel untuk mahasiswa baru.</p>',
                    ]
                ],
            ]
        );

        Proposal::updateOrCreate(
            ['title' => 'Pengajuan Lomba Cerdas Cermat IT 2026'],
            [
                'user_id' => $user->id,
                'month'   => 10,
                'year'    => 2026,
                'status'  => 'approved',
                'content' => [
                    [
                        'name'        => 'Babak Penyisihan',
                        'date'        => '2026-10-15',
                        'location'    => 'Gedung Serbaguna',
                        'description' => '<p>Babak penyisihan dengan sistem tes tulis berbasis CBT.</p>',
                    ],
                    [
                        'name'        => 'Babak Final',
                        'date'        => '2026-10-16',
                        'location'    => 'Gedung Serbaguna',
                        'description' => '<p>Babak adu cepat tepat untuk 3 tim terbaik.</p>',
                    ]
                ],
            ]
        );

        // --- 5. Laporan (LPJ) ---
        Report::updateOrCreate(
            ['title' => 'LPJ Orientasi Mahasiswa Baru 2026'],
            [
                'user_id' => $user->id,
                'month'   => 7,
                'year'    => 2026,
                'status'  => 'finalized',
                'content' => [
                    [
                        'name'        => 'Penyambutan Maba',
                        'date'        => '2026-07-25',
                        'location'    => 'Lapangan Utama',
                        'description' => '<p>Kegiatan berjalan lancar, dihadiri oleh Rektor dan seluruh jajaran dekan. Tidak ada insiden berarti.</p>',
                    ]
                ],
            ]
        );

        $this->command->info('Data seeder dummy lengkap berhasil ditambahkan untuk semua tabel!');
    }
}
