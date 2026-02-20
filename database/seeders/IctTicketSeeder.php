<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IctTicketSeeder extends Seeder
{
    /**
     * Seed dummy ICT Helpdesk tickets for dashboard testing.
     */
    public function run(): void
    {
        // Get all user IDs from admins table
        $userIds = DB::table('admins')->pluck('id')->toArray();
        if (empty($userIds)) {
            $this->command->error('No users found in admins table!');
            return;
        }

        // ICT staff who can be assigned tickets (first 5 users as IT team)
        $ictStaffIds = array_slice($userIds, 0, 5);

        // Realistic ticket subjects per category
        $subjects = [
            'Hardware' => [
                'Laptop tidak bisa menyala',
                'Keyboard rusak / tidak berfungsi',
                'Mouse wireless mati',
                'Monitor berkedip-kedip',
                'Printer tidak bisa print',
                'Scanner error saat scanning dokumen',
                'UPS bunyi beep terus-menerus',
                'Laptop overheat dan mati sendiri',
                'Hard disk penuh / perlu upgrade',
                'RAM laptop perlu ditambah',
                'Kabel LAN putus di ruangan',
                'Proyektor ruang meeting error',
                'Headset audio tidak keluar suara',
                'Webcam laptop tidak terdeteksi',
                'Baterai laptop drop / cepat habis',
            ],
            'Software' => [
                'Microsoft Office tidak bisa dibuka',
                'Windows Update gagal / stuck',
                'Antivirus expired perlu diperbarui',
                'Aplikasi ERP error saat login',
                'Email Outlook tidak bisa kirim email',
                'VPN tidak bisa connect ke server',
                'Browser Chrome crash terus-menerus',
                'Driver printer perlu diinstall',
                'Aplikasi zoom tidak bisa share screen',
                'Software lisensi mau expired',
                'Install aplikasi baru untuk departemen',
                'Sistem backup gagal berjalan',
                'Adobe Acrobat tidak bisa edit PDF',
                'Excel macro error saat dijalankan',
                'Laptop perlu install ulang OS',
            ],
            'Network' => [
                'WiFi kantor tidak bisa connect',
                'Internet lambat sekali hari ini',
                'Tidak bisa akses shared folder',
                'Email tidak bisa kirim/terima',
                'Akses ke website tertentu diblokir',
                'VPN putus-putus / tidak stabil',
                'Printer network tidak terdeteksi',
                'Tidak bisa remote desktop ke server',
                'File server tidak bisa diakses',
                'Jaringan LAN di lantai 2 mati total',
                'Access point WiFi perlu restart',
                'Bandwidth internet perlu ditambah',
            ],
            'Account' => [
                'Lupa password email kantor',
                'Akun Windows terkunci (locked out)',
                'Perlu buat akun email baru karyawan',
                'Reset password aplikasi ERP',
                'Hapus akun karyawan yang resign',
                'Perlu akses ke folder departemen lain',
                'Ganti password WiFi kantor',
                'Akun Microsoft 365 perlu diaktifkan',
                'Request akses VPN untuk WFH',
                'Tambah user baru ke grup Active Directory',
                'Update data karyawan di sistem',
                'Akun email penuh / over quota',
            ],
        ];

        $descriptions = [
            'Mohon segera ditangani karena mengganggu pekerjaan sehari-hari.',
            'Sudah coba restart beberapa kali tapi tetap sama masalahnya.',
            'Masalah ini sudah terjadi sejak kemarin dan belum ada perbaikan.',
            'Butuh bantuan segera karena ada deadline pekerjaan penting.',
            'Terima kasih atas bantuannya, mohon prioritaskan penyelesaiannya.',
            'Kendala ini membuat saya tidak bisa bekerja dengan normal.',
            'Sudah mencoba troubleshoot sendiri tapi belum berhasil.',
            'Tolong dibantu ya, karena urusan ini cukup urgent.',
            'Masalah ini terjadi secara intermittent, kadang bisa kadang tidak.',
            'Mohon dijadwalkan untuk pengecekan ke meja saya.',
        ];

        $categories = ['Hardware', 'Software', 'Network', 'Account'];
        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        $statuses = ['Open', 'In Progress', 'Resolved', 'Closed'];

        // Priority distribution weights (more Medium, less Critical)
        $priorityWeights = ['Low' => 20, 'Medium' => 45, 'High' => 25, 'Critical' => 10];
        // Category distribution weights
        $categoryWeights = ['Hardware' => 25, 'Software' => 35, 'Network' => 20, 'Account' => 20];

        $tickets = [];
        $now = Carbon::now();

        // Generate tickets over the last 3 months for trends
        $ticketNumber = 1;

        // Month distribution: more tickets in recent months
        $monthConfigs = [
            ['start' => $now->copy()->subMonths(2)->startOfMonth(), 'end' => $now->copy()->subMonths(2)->endOfMonth(), 'count' => 25],
            ['start' => $now->copy()->subMonth()->startOfMonth(), 'end' => $now->copy()->subMonth()->endOfMonth(), 'count' => 35],
            ['start' => $now->copy()->startOfMonth(), 'end' => $now, 'count' => 45],
        ];

        foreach ($monthConfigs as $monthConfig) {
            for ($i = 0; $i < $monthConfig['count']; $i++) {
                $category = $this->weightedRandom($categoryWeights);
                $priority = $this->weightedRandom($priorityWeights);
                $subjectList = $subjects[$category];
                $subject = $subjectList[array_rand($subjectList)];
                $description = $descriptions[array_rand($descriptions)];

                // Random creation date within the month
                $diffSeconds = $monthConfig['start']->diffInSeconds($monthConfig['end']);
                $randomSeconds = rand(0, max(0, $diffSeconds));
                $createdAt = $monthConfig['start']->copy()->addSeconds($randomSeconds);

                // Determine status based on age and randomness
                $hoursOld = $createdAt->diffInHours($now);

                if ($monthConfig === end($monthConfigs) && $i >= $monthConfig['count'] - 12) {
                    // Last 12 tickets of current month: mix of Open and In Progress
                    $status = $this->weightedRandom([
                        'Open' => 40,
                        'In Progress' => 35,
                        'Resolved' => 15,
                        'Closed' => 10,
                    ]);
                } elseif ($hoursOld > 72) {
                    // Older tickets are mostly resolved/closed
                    $status = $this->weightedRandom([
                        'Open' => 3,
                        'In Progress' => 7,
                        'Resolved' => 40,
                        'Closed' => 50,
                    ]);
                } else {
                    $status = $this->weightedRandom([
                        'Open' => 25,
                        'In Progress' => 30,
                        'Resolved' => 30,
                        'Closed' => 15,
                    ]);
                }

                // Assigned to ICT staff (always assigned if In Progress/Resolved/Closed)
                $assignedTo = null;
                if (in_array($status, ['In Progress', 'Resolved', 'Closed'])) {
                    $assignedTo = $ictStaffIds[array_rand($ictStaffIds)];
                } elseif ($status === 'Open' && rand(1, 100) <= 30) {
                    $assignedTo = $ictStaffIds[array_rand($ictStaffIds)];
                }

                // Resolved at timestamp
                $resolvedAt = null;
                if (in_array($status, ['Resolved', 'Closed'])) {
                    // Resolution time varies: 1-72 hours after creation
                    $resolutionHours = match ($priority) {
                        'Critical' => rand(1, 12),
                        'High' => rand(2, 24),
                        'Medium' => rand(4, 48),
                        'Low' => rand(8, 72),
                    };
                    $resolvedAt = $createdAt->copy()->addHours($resolutionHours);
                    // Don't let resolved_at be in the future
                    if ($resolvedAt->isAfter($now)) {
                        $resolvedAt = $now->copy()->subMinutes(rand(10, 120));
                    }
                }

                $monthStr = $createdAt->format('Ym');
                $ticketNum = str_pad($ticketNumber, 5, '0', STR_PAD_LEFT);

                $tickets[] = [
                    'user_id' => $userIds[array_rand($userIds)],
                    'ticket_number' => "TKT-{$monthStr}-{$ticketNum}",
                    'subject' => $subject,
                    'category' => $category,
                    'priority' => $priority,
                    'description' => $description,
                    'attachment' => null,
                    'status' => $status,
                    'assigned_to' => $assignedTo,
                    'resolved_at' => $resolvedAt?->format('Y-m-d H:i:s'),
                    'created_at' => $createdAt->format('Y-m-d H:i:s'),
                    'updated_at' => ($resolvedAt ?? $createdAt)->format('Y-m-d H:i:s'),
                ];

                $ticketNumber++;
            }
        }

        // Clear existing tickets and insert
        DB::table('ict_tickets')->truncate();

        // Insert in chunks
        foreach (array_chunk($tickets, 25) as $chunk) {
            DB::table('ict_tickets')->insert($chunk);
        }

        $statusCounts = collect($tickets)->groupBy('status')->map->count();
        $this->command->info("✅ Created " . count($tickets) . " dummy ICT tickets:");
        $this->command->info("   Open: " . ($statusCounts['Open'] ?? 0));
        $this->command->info("   In Progress: " . ($statusCounts['In Progress'] ?? 0));
        $this->command->info("   Resolved: " . ($statusCounts['Resolved'] ?? 0));
        $this->command->info("   Closed: " . ($statusCounts['Closed'] ?? 0));
    }

    /**
     * Weighted random selection
     */
    private function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        $rand = rand(1, $total);
        $current = 0;
        foreach ($weights as $key => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $key;
            }
        }
        return array_key_first($weights);
    }
}
