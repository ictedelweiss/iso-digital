<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Location;
use App\Models\Asset;
use App\Models\User;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories
        $categories = [
            ['name' => 'IT Equipment', 'prefix' => 'IT', 'description' => 'Information Technology and Computer Equipment'],
            ['name' => 'Furniture', 'prefix' => 'FUR', 'description' => 'Office Furniture and Fixtures'],
            ['name' => 'Vehicles', 'prefix' => 'VEH', 'description' => 'Company Vehicles and Transportation'],
            ['name' => 'Office Equipment', 'prefix' => 'OFF', 'description' => 'General Office Equipment'],
            ['name' => 'Building', 'prefix' => 'BLD', 'description' => 'Building and Infrastructure'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
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
            Location::create($location);
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

        // Create sample assets
        $itCategory = Category::where('prefix', 'IT')->first();
        $furCategory = Category::where('prefix', 'FUR')->first();
        $offCategory = Category::where('prefix', 'OFF')->first();
        $hqLocation = Location::where('code', 'HQ')->first();
        $br1Location = Location::where('code', 'BR1')->first();

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
            Asset::create($assetData);
        }

        $this->command->info('Asset seeder completed successfully!');
        $this->command->info('Created: ' . count($categories) . ' categories');
        $this->command->info('Created: ' . count($locations) . ' locations');
        $this->command->info('Created: ' . count($sampleAssets) . ' assets');
    }
}
