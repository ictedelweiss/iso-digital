<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $employees = [
            ['name' => 'ABI', 'email' => ''],
            ['name' => 'Ade Ayu Puspitawati', 'email' => 'ayu.puspitawati@edelweiss.sch.id'],
            ['name' => 'Anggraeni Novianti', 'email' => 'anggraeni.novianti@edelweiss.sch.id'],
            ['name' => 'Ani Andriani', 'email' => 'ani.andriani@edelweiss.sch.id'],
            ['name' => 'Aris Setyawan', 'email' => 'aris.setyawan@edelweiss.sch.id'],
            ['name' => 'Armitridesi Shinta Marit', 'email' => 'armitridesi.marito@edelweiss.sch.id'],
            ['name' => 'Cessy Wahyudi', 'email' => ''],
            ['name' => 'Diah Nurhayati', 'email' => 'diah.nurhayati@edelweiss.sch.id'],
            ['name' => 'Dian Susanto', 'email' => ''],
            ['name' => 'Difa Adelia Adzra Nabill', 'email' => 'difa.adelia@edelweiss.sch.id'],
            ['name' => 'Ella Dardanella', 'email' => 'ella.dardanella@edelweiss.sch.id'],
            ['name' => 'Entol Ammar Dzaky', 'email' => 'entol.dzaky@edelweiss.sch.id'],
            ['name' => 'Ermalia Mutiara Wadi', 'email' => 'ermalia.mutiara@edelweiss.sch.id'],
            ['name' => 'Febriana', 'email' => 'febriana@edelweiss.sch.id'],
            ['name' => 'Gilang Fadilan Syabanu', 'email' => 'gilang.fadilan@edelweiss.sch.id'],
            ['name' => 'Hana Sabila Azka', 'email' => 'hanazka@edelweiss.sch.id'],
            ['name' => 'Husin', 'email' => ''],
            ['name' => 'Ketut Dewi Laksmi', 'email' => 'ketut.laksmi@edelweiss.sch.id'],
            ['name' => 'Khansa Az Zahra', 'email' => 'khansa.azzahra@edelweiss.sch.id'],
            ['name' => 'Khoirunnisa Nurfalah', 'email' => 'khoirunnisa.nurfalah@edelweiss.sch.id'],
            ['name' => 'M. Ridwan', 'email' => ''],
            ['name' => 'Madinah', 'email' => ''],
            ['name' => 'Makiah', 'email' => 'makiah@edelweiss.sch.id'],
            ['name' => 'Mamat', 'email' => ''],
            ['name' => 'Martha Putri Anggraeni', 'email' => 'martha.putri@edelweiss.sch.id'],
            ['name' => 'Medina Marpaung', 'email' => 'medina.marpaung@edelweiss.sch.id'],
            ['name' => 'Mei Triastuik', 'email' => 'mei.triastuik@edelweiss.sch.id'],
            ['name' => 'Mia Roosmalisa Dewi', 'email' => 'mia.roosmalisa@edelweiss.sch.id'],
            ['name' => 'Mikhail Adi Nogoro', 'email' => 'mikhail.joeaningrat@edelweiss.sch.id'],
            ['name' => 'Miske Ferlani', 'email' => 'miske.ferlani@edelweiss.sch.id'],
            ['name' => 'Muhammad Bimo Widihardjo', 'email' => 'muhammad.widihardjo@edelweiss.sch.id'],
            ['name' => 'Murdiono', 'email' => 'murdiono.teacher@edelweiss.sch.id'],
            ['name' => 'Mutiara Dewi Agustin', 'email' => 'mutiara.agustin@edelweiss.sch.id'],
            ['name' => 'Neni Nurhanayani', 'email' => ''],
            ['name' => 'Ni Ketut Swasitiri', 'email' => 'ketut.swasitiri@edelweiss.sch.id'],
            ['name' => 'Nisrina Tasya', 'email' => 'nisrina.tasya@edelweiss.sch.id'],
            ['name' => 'Novelinda Putri Cahyanti', 'email' => ''],
            ['name' => 'Novita Sari', 'email' => 'novitasari@edelweiss.sch.id'],
            ['name' => 'Osy Harisa', 'email' => 'osy.harisa@edelweiss.sch.id'],
            ['name' => 'Permata Chitra Haelda', 'email' => 'permata.manik@edelweiss.sch.id'],
            ['name' => 'Pradinya Paramitha', 'email' => ''],
            ['name' => 'Puji Utami', 'email' => 'puji.utami@edelweiss.sch.id'],
            ['name' => 'Putri Istiqomah', 'email' => 'putri.istiqomah@edelweiss.sch.id'],
            ['name' => 'Rachma Fairuz Dhiany', 'email' => 'rachma.fairuz@edelweiss.sch.id'],
            ['name' => 'Ratna Kurnia Syifa', 'email' => 'ratna.kurnia@edelweiss.sch.id'],
            ['name' => 'Renatta Augusta', 'email' => 'renatta.augusta@edelweiss.sch.id'],
            ['name' => 'Revigre Ernala Barus', 'email' => 'revigre.barus@edelweiss.sch.id'],
            ['name' => 'Rina Triyans', 'email' => 'rina.triyans@edelweiss.sch.id'],
            ['name' => 'Rizka Alaeky Savana', 'email' => 'rizka.savana@edelweiss.sch.id'],
            ['name' => 'Roro Hanum Rahayuanum P', 'email' => ''],
            ['name' => 'Rudi Mahfudin', 'email' => 'rudi.mahfudin@edelweiss.sch.id'],
            ['name' => 'Safira Aprilia', 'email' => 'safira.aprilia@edelweiss.sch.id'],
            ['name' => 'Sarah Melinda Sari', 'email' => 'sarah.sari@edelweiss.sch.id'],
            ['name' => 'Saskya Adrita Ramadhanti', 'email' => 'saskya.ramadhanti@edelweiss.sch.id'],
            ['name' => 'Siti Juwairiyah', 'email' => 'siti.juwairiyah@edelweiss.sch.id'],
            ['name' => 'Suryadi', 'email' => 'suryadi.teacher@edelweiss.sch.id'],
            ['name' => 'Swilia Galih Puspa', 'email' => 'swilia.puspa@edelweiss.sch.id'],
            ['name' => 'Talitha Djulia Claresta', 'email' => 'talitha.claresta@edelweiss.sch.id'],
            ['name' => 'Tata Sastra', 'email' => ''],
            ['name' => 'Theophila Heronova Sagal', 'email' => 'theophila.sagal@edelweiss.sch.id'],
            ['name' => 'Titis Rahmawati Wijiastuti', 'email' => 'titis.wijiastuti@edelweiss.sch.id'],
            ['name' => 'Tjut Dwi Anggarani', 'email' => 'tjutdwi@edelweiss.sch.id'],
            ['name' => 'Tobias', 'email' => 'tobias.gao@edelweiss.sch.id'],
            ['name' => 'Tohirun', 'email' => ''],
            ['name' => 'Tri Yuliastuti', 'email' => 'tri.yuliastuti@edelweiss.sch.id'],
            ['name' => 'Ulfha Safitri', 'email' => 'ulfha.safitri@edelweiss.sch.id'],
            ['name' => 'Yosepha Tobefy', 'email' => 'yosepha.gao@edelweiss.sch.id'],
            ['name' => 'Yudha Purnama', 'email' => 'yudha.purnama@edelweiss.sch.id'],
            ['name' => 'Zukarnain', 'email' => ''],
        ];

        $defaultPassword = Hash::make('edelweiss2024'); // Default password untuk semua user

        foreach ($employees as $employee) {
            // Generate username dari nama (lowercase, replace space dengan dot)
            $username = strtolower(str_replace(' ', '.', $employee['name']));

            // Jika email kosong, generate dari username
            $email = !empty($employee['email']) ? $employee['email'] : $username . '@edelweiss.sch.id';

            // Check if user already exists
            $exists = DB::table('admins')->where('username', $username)->exists();

            if (!$exists) {
                DB::table('admins')->insert([
                    'username' => $username,
                    'password_hash' => $defaultPassword,
                    'ms_email' => $email,
                    'role' => 'user',
                    'created_at' => now(),
                ]);

                echo "✅ Created user: {$employee['name']} ({$username})\n";
            } else {
                echo "⏭️  Skipped (already exists): {$employee['name']}\n";
            }
        }

        echo "\n✅ Employee import completed!\n";
        echo "Default password for all users: edelweiss2024\n";
    }
}
