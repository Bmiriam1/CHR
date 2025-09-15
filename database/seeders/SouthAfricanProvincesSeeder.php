<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SouthAfricanProvincesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            ['name' => 'Gauteng'],
            ['name' => 'Western Cape'],
            ['name' => 'KwaZulu-Natal'],
            ['name' => 'Eastern Cape'],
            ['name' => 'Free State'],
            ['name' => 'Limpopo'],
            ['name' => 'Mpumalanga'],
            ['name' => 'Northern Cape'],
            ['name' => 'North West'],
        ];

        foreach ($provinces as $province) {
            Province::firstOrCreate(
                ['name' => $province['name']],
                $province
            );
        }

        $this->command->info('South African provinces seeded successfully!');
    }
}
