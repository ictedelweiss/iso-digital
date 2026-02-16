<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Location;
use App\Models\Asset;
use App\Models\User;
use Faker\Factory as Faker;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Create sample categories
        $categories = [
            ['name' => 'IT Equipment', 'prefix' => 'IT', 'description' => 'Information Technology and Computer Equipment'],
            ['name' => 'Furniture', 'prefix' => 'FUR', 'description' => 'Office Furniture and Fixtures'],
            ['name' => 'Vehicles', 'prefix' => 'VEH', 'description' => 'Company Vehicles and Transportation'],
            ['name' => 'Office Equipment', 'prefix' => 'OFF', 'description' => 'General Office Equipment'],
            ['name' => 'Building', 'prefix' => 'BLD', 'description' => 'Building and Infrastructure'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
            ['name' => $category['name']],
                $category
            );
        }

        // Create sample locations
        $locations = [
            ['name' => 'Head Quarter', 'code' => 'HQ', 'address' => 'Jl. Sudirman No. 123, Jakarta Pusat'],
            ['name' => 'Branch 1 - Surabaya', 'code' => 'BR1', 'address' => 'Jl. Basuki Rahmat No. 45, Surabaya'],
            ['name' => 'Branch 2 - Bandung', 'code' => 'BR2', 'address' => 'Jl. Asia Afrika No. 78, Bandung'],
            ['name' => 'Warehouse', 'code' => 'WH', 'address' => 'Jl. Raya Bekasi KM 20, Bekasi'],
            ['name' => 'Remote Office', 'code' => 'RMT', 'address' => 'Work From Home Locations'],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
            ['name' => $location['name']],
                $location
            );
        }

        // Get first user as creator (or create one if doesn't exist)
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Get all categories and locations
        $allCategories = Category::all();
        $allLocations = Location::all();

        // Create sample assets (Manual)
        // Find categories/locations by name to be safe
        $itCategory = Category::where('name', 'IT Equipment')->first();
        $furCategory = Category::where('name', 'Furniture')->first();
        $offCategory = Category::where('name', 'Office Equipment')->first();
        $hqLocation = Location::where('name', 'Head Quarter')->first();
        $br1Location = Location::where('name', 'Branch 1 - Surabaya')->first();

        // Safety check
        if ($itCategory && $furCategory && $offCategory && $hqLocation && $br1Location) {
            $sampleAssets = [
                // IT Equipment
                [
                    'name' => 'Laptop Dell Latitude 5420',
                    'category_id' => $itCategory->id,
                    'location_id' => $hqLocation->id,
                    'serial_number' => 'DLAT5420001',
                    'model' => 'Latitude 5420',
                    'manufacturer' => 'Dell',
                    'purchase_date' => '2023-06-15',
                    'purchase_price' => 15000000,
                    'status' => 'Active',
                    'condition' => 'Excellent',
                    'description' => 'Laptop untuk staff IT Department',
                ],
                [
                    'name' => 'MacBook Pro 14"',
                    'category_id' => $itCategory->id,
                    'location_id' => $hqLocation->id,
                    'serial_number' => 'MBP14001',
                    'model' => 'MacBook Pro 14inch 2023',
                    'manufacturer' => 'Apple',
                    'purchase_date' => '2023-09-20',
                    'purchase_price' => 28000000,
                    'status' => 'Active',
                    'condition' => 'Excellent',
                    'description' => 'Laptop untuk Design Team',
                ],
                [
                    'name' => 'Dell Monitor 27"',
                    'category_id' => $itCategory->id,
                    'location_id' => $hqLocation->id,
                    'serial_number' => 'DM27001',
                    'model' => 'P2722H',
                    'manufacturer' => 'Dell',
                    'purchase_date' => '2023-05-10',
                    'purchase_price' => 3500000,
                    'status' => 'Active',
                    'condition' => 'Good',
                    'description' => 'Monitor tambahan untuk workstation',
                ],
                [
                    'name' => 'HP Printer LaserJet Pro',
                    'category_id' => $itCategory->id,
                    'location_id' => $br1Location->id,
                    'serial_number' => 'HPLJ001',
                    'model' => 'LaserJet Pro M404dn',
                    'manufacturer' => 'HP',
                    'purchase_date' => '2022-11-15',
                    'purchase_price' => 4500000,
                    'status' => 'Maintenance',
                    'condition' => 'Fair',
                    'description' => 'Printer untuk Branch 1',
                    'notes' => 'Perlu service bulanan',
                ],
                // Furniture
                [
                    'name' => 'Meja Kerja Ergonomis',
                    'category_id' => $furCategory->id,
                    'location_id' => $hqLocation->id,
                    'model' => 'Standing Desk Pro',
                    'manufacturer' => 'IKEA',
                    'purchase_date' => '2023-02-20',
                    'purchase_price' => 2500000,
                    'status' => 'Active',
                    'condition' => 'Good',
                    'description' => 'Meja kerja dengan standing feature',
                ],
                [
                    'name' => 'Kursi Kantor Herman Miller',
                    'category_id' => $furCategory->id,
                    'location_id' => $hqLocation->id,
                    'model' => 'Aeron Chair',
                    'manufacturer' => 'Herman Miller',
                    'purchase_date' => '2023-03-10',
                    'purchase_price' => 8000000,
                    'status' => 'Active',
                    'condition' => 'Excellent',
                    'description' => 'Kursi ergonomis premium',
                ],
                // Office Equipment
                [
                    'name' => 'Proyektor Epson',
                    'category_id' => $offCategory->id,
                    'location_id' => $hqLocation->id,
                    'serial_number' => 'EPSON001',
                    'model' => 'EB-X49',
                    'manufacturer' => 'Epson',
                    'purchase_date' => '2022-08-15',
                    'purchase_price' => 6500000,
                    'status' => 'Active',
                    'condition' => 'Good',
                    'description' => 'Proyektor untuk meeting room',
                ],
                [
                    'name' => 'Whiteboard 2x3m',
                    'category_id' => $offCategory->id,
                    'location_id' => $hqLocation->id,
                    'manufacturer' => 'Standard Board',
                    'purchase_date' => '2023-01-10',
                    'purchase_price' => 1500000,
                    'status' => 'Active',
                    'condition' => 'Good',
                    'description' => 'Whiteboard untuk collaboration room',
                ],
            ];

            foreach ($sampleAssets as $assetData) {
                // Check if exist by name (simple check to avoid duplicate on re-run)
                if (!Asset::where('name', $assetData['name'])->exists()) {
                    Asset::create($assetData);
                }
            }
        }

        // Generate ~50 Random Assets
        $this->command->info('Generating 50 random assets...');

        for ($i = 0; $i < 50; $i++) {
            $category = $allCategories->random();
            $location = $allLocations->random();

            $manufacturers = ['Dell', 'HP', 'Lenovo', 'Apple', 'Asus', 'Acer', 'IKEA', 'Informa', 'Epson', 'Canon'];
            $conditions = ['Excellent', 'Good', 'Fair', 'Poor'];
            $statuses = ['Active', 'Active', 'Active', 'Maintenance', 'Retired']; // Weighted towards Active

            $purchaseDate = $faker->dateTimeBetween('-5 years', 'now');

            Asset::create([
                'name' => $this->generateAssetName($category, $faker),
                'category_id' => $category->id,
                'location_id' => $location->id,
                'serial_number' => strtoupper($faker->bothify('SN-????-#####')),
                'model' => strtoupper($faker->bothify('MDL-###')),
                'manufacturer' => $faker->randomElement($manufacturers),
                'purchase_date' => $purchaseDate,
                'purchase_price' => $faker->numberBetween(1000000, 50000000),
                'status' => $faker->randomElement($statuses),
                'condition' => $faker->randomElement($conditions),
                'description' => $faker->sentence(),
                'notes' => $faker->optional(0.3)->sentence(), // 30% chance of notes
                'created_by' => $user->id,
            ]);
        }

        $this->command->info('Asset seeder completed successfully!');
        $this->command->info('Created: ' . count($categories) . ' categories');
        $this->command->info('Created: ' . count($locations) . ' locations');
        $this->command->info('Created: ' . (count($sampleAssets) + 50) . ' assets (approx)');
    }

    private function generateAssetName($category, $faker)
    {
        $prefix = $category->prefix;
        switch ($prefix) {
            case 'IT':
                return $faker->randomElement(['Laptop', 'PC Desktop', 'Monitor', 'Server', 'Switch', 'Router']) . ' ' . $faker->company;
            case 'FUR':
                return $faker->randomElement(['Meja Kerja', 'Kursi Kantor', 'Lemari Arsip', 'Sofa Tamu']) . ' ' . $faker->colorName;
            case 'VEH':
                return $faker->randomElement(['Mobil Operasional', 'Motor Kurir', 'Truk Box']) . ' ' . $faker->year;
            case 'OFF':
                return $faker->randomElement(['Mesin Fotokopi', 'Printer', 'Scanner', 'Paper Shredder']) . ' ' . $faker->company;
            case 'BLD':
                return $faker->randomElement(['Gedung Utama', 'Pos Satpam', 'Kantin', 'Toilet']) . ' ' . $faker->city;
            default:
                return 'Asset ' . $faker->word;
        }
    }
}