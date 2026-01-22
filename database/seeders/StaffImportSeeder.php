<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffImportSeeder extends Seeder
{
    public function run()
    {
        $staff = [
            ['ABI', ''],
            ['Ade Ayu Puspitawati', 'ayu.puspitawati@edelweiss.sch.id'],
            ['Anggraeni Novianti', 'anggraeni.novianti@edelweiss.sch.id'],
            ['Ani Andriani', 'ani.andriani@edelweiss.sch.id'],
            ['Aris Setyawan', 'aris.setyawan@edelweiss.sch.id'],
            ['Armitridesi Shinta Marit', 'armitridesi.marito@edelweiss.sch.id'],
            ['Cecep Wahyudi', ''],
            ['Diah Nurhayati', 'diah.nurhayati@edelweiss.sch.id'],
            ['Dian Susanto', ''],
            ['Difa Adelia Adzra Nabill', 'difa.adelia@edelweiss.sch.id'],
            ['Ella Dardanella', 'ella.dardanella@edelweiss.sch.id'],
            ['Entol Ammar Dzaky', 'entol.dzaky@edelweiss.sch.id'],
            ['Ermalia Mutiara Wadi', 'ermalia.mutiara@edelweiss.sch.id'],
            ['Febriana', 'febriana@edelweiss.sch.id'],
            ['Gilang Fadhlan Syabanu', 'gilang.fadhlan@edelweiss.sch.id'],
            ['Hana Sabila Azka', 'hanazka@edelweiss.sch.id'],
            ['Husin', ''],
            ['Ketut Dewi Laksmi', 'ketut.laksmi@edelweiss.sch.id'],
            ['Khansa Az Zahra', 'khansa.azzahra@edelweiss.sch.id'],
            ['Khoirunnisa Nurfalah', 'khoirunnisa.nurfalah@edelweiss.sch.id'],
            ['M. Ridwan', ''],
            ['Madinah', ''],
            ['Makiah', 'makiah@edelweiss.sch.id'],
            ['Mamat', ''],
            ['Martha Putri Anggraeni', 'martha.putri@edelweiss.sch.id'],
            ['Medina Marpaung', 'medina.marpaung@edelweiss.sch.id'],
            ['Mei Triastutik', 'mei.triastutik@edelweiss.sch.id'],
            ['Mia Roosmalisa Dewi', 'mia.roosmalisa@edelweiss.sch.id'],
            ['Mikhail Adi Negoro', 'mikhail.pasaribu@edelweiss.sch.id'],
            ['Miske Ferlani', 'miske.ferlani@edelweiss.sch.id'],
            ['Muhammad Bimo Widihardjo', 'muhammad.widihardjo@edelweiss.sch.id'],
            ['Murdiono', 'murdiono.teacher@edelweiss.sch.id'],
            ['Mutiara Dewi Agustin', 'mutiara.agustin@edelweiss.sch.id'],
            ['Neni Nurhandayani', ''],
            ['Ni Ketut Swastitri', 'ketut.swastitri@edelweiss.sch.id'],
            ['Nisrina Tasya', 'nisrina.tasya@edelweiss.sch.id'],
            ['Novelinda Putri Cahyanti', ''],
            ['Novita Sari', 'novitasari@edelweiss.sch.id'],
            ['Osy Harisa', 'osy.harisa@edelweiss.sch.id'],
            ['Permata Chitra Haelda', 'permata.manik@edelweiss.sch.id'],
            ['Pradjnya Paramitha', ''],
            ['Puji Utami', 'puji.utami@edelweiss.sch.id'],
            ['Putri Istiqomah', 'putri.istiqomah@edelweiss.sch.id'],
            ['Rachma Fairuz Dhiany', 'rachma.fairuz@edelweiss.sch.id'],
            ['Ratna Kurnia Syifa', 'ratna.kurnia@edelweiss.sch.id'],
            ['Renatta Augusta', 'renatta.augusta@edelweiss.sch.id'],
            ['Revigrie Ernala Barus', 'revigrie.barus@edelweiss.sch.id'],
            ['Rina Triyana', 'rina.triyana@edelweiss.sch.id'],
            ['Rizka Alaedya Savana', 'rizka.savana@edelweiss.sch.id'],
            ['Roro Hanum Rahayuanum P', ''],
            ['Rudi Mahfudin', 'rudi.mahfudin@edelweiss.sch.id'],
            ['Safira Aprillia', 'safira.aprillia@edelweiss.sch.id'],
            ['Sarah Melinda Sari', 'sarah.sari@edelweiss.sch.id'],
            ['Saskya Adrila Ramadhanti', 'saskya.ramadhanti@edelweiss.sch.id'],
            ['Siti Juwairiyah', 'siti.juwairiyah@edelweiss.sch.id'],
            ['Suryadi', 'suryadi.teacher@edelweiss.sch.id'],
            ['Swilia Galih Puspa', 'swilia.puspa@edelweiss.sch.id'],
            ['Talitha Djulia Claresta', 'talitha.claresta@edelweiss.sch.id'],
            ['Tata Sastra', ''],
            ['Theopilia Heronova Sagal', 'theopilia.sagala@edelweiss.sch.id'],
            ['Titis Rahmawati Wijiastu', 'titis.wijiastuti@edelweiss.sch.id'],
            ['Tjut Dwi Anggraini', 'tjut.dwi@edelweiss.sch.id'],
            ['Tobias', 'tobias.gang@edelweiss.sch.id'],
            ['Tohir', ''],
            ['Tri Yuliastuti', 'tri.yuliastuti@edelweiss.sch.id'],
            ['Vika Agustin', 'vika.agustin@edelweiss.sch.id'],
            ['Yosepha Tobefy', 'yosepha.gaol@edelweiss.sch.id'],
            ['Yudha Purnama', 'yudha.purnama@edelweiss.sch.id'],
            ['sukarman', ''],
        ];

        $defaultPassword = Hash::make('edelweiss2024');

        foreach ($staff as $person) {
            $name = $person[0];
            $email = $person[1];

            // Generate username from email (if exists) or name (converted to lowercase slug)
            if (!empty($email)) {
                $username = explode('@', $email)[0];
            } else {
                $username = Str::slug($name, '.');
            }

            // Check if user exists by display_name or ms_email or username
            $exists = DB::table('admins')
                ->where('display_name', $name)
                ->orWhere('username', $username)
                ->orWhere('ms_email', $email)
                ->first();

            if ($exists) {
                // Update existing user information
                DB::table('admins')
                    ->where('id', $exists->id)
                    ->update([
                        'display_name' => $name,
                        'ms_email' => !empty($email) ? $email : $exists->ms_email,
                        'username' => $username, // Normalize username
                    ]);
                $this->command->info("Updated: $name");
            } else {
                // Insert new user
                DB::table('admins')->insert([
                    // IMPORTANT: Admin panel uses 'password_hash' column, NOT 'password'
                    'username' => $username,
                    'password_hash' => $defaultPassword,
                    'display_name' => $name,
                    'ms_email' => $email ?: null,
                    'role' => 'Staff', // Default role
                ]);
                $this->command->info("Created: $name");
            }
        }
    }
}
