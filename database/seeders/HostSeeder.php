<?php

namespace Database\Seeders;

use App\Models\Host;
use App\Models\Program;
use App\Models\Company;
use Illuminate\Database\Seeder;

class HostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get companies and programs
        $companies = Company::with('programs')->get();

        if ($companies->isEmpty()) {
            $this->command->warn('No companies found. Please run company and program seeders first.');
            return;
        }

        $hostData = [
            [
                'name' => 'Cape Town Training Center',
                'description' => 'Main training facility in Cape Town CBD',
                'address_line1' => '123 Main Street',
                'address_line2' => 'Floor 2',
                'city' => 'Cape Town',
                'province' => 'Western Cape',
                'postal_code' => '8001',
                'country' => 'South Africa',
                'latitude' => -33.9249,
                'longitude' => 18.4241,
                'radius_meters' => 100,
                'contact_person' => 'John Smith',
                'contact_phone' => '+27 21 123 4567',
                'contact_email' => 'john.smith@example.com',
                'requires_gps_validation' => true,
                'requires_time_validation' => true,
                'check_in_start_time' => '08:00',
                'check_in_end_time' => '09:30',
                'check_out_start_time' => '16:00',
                'check_out_end_time' => '18:00',
                'max_daily_check_ins' => 2,
                'allow_multiple_check_ins' => true,
                'require_supervisor_approval' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Johannesburg Skills Hub',
                'description' => 'Modern skills development center in Sandton',
                'address_line1' => '456 Rivonia Road',
                'address_line2' => 'Sandton City',
                'city' => 'Johannesburg',
                'province' => 'Gauteng',
                'postal_code' => '2196',
                'country' => 'South Africa',
                'latitude' => -26.1076,
                'longitude' => 28.0567,
                'radius_meters' => 150,
                'contact_person' => 'Sarah Johnson',
                'contact_phone' => '+27 11 987 6543',
                'contact_email' => 'sarah.j@example.com',
                'requires_gps_validation' => true,
                'requires_time_validation' => false,
                'check_in_start_time' => null,
                'check_in_end_time' => null,
                'check_out_start_time' => null,
                'check_out_end_time' => null,
                'max_daily_check_ins' => 3,
                'allow_multiple_check_ins' => true,
                'require_supervisor_approval' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Durban Coastal Campus',
                'description' => 'Seaside learning facility with ocean views',
                'address_line1' => '789 Marine Parade',
                'address_line2' => null,
                'city' => 'Durban',
                'province' => 'KwaZulu-Natal',
                'postal_code' => '4001',
                'country' => 'South Africa',
                'latitude' => -29.8579,
                'longitude' => 31.0292,
                'radius_meters' => 200,
                'contact_person' => 'Mike Wilson',
                'contact_phone' => '+27 31 456 7890',
                'contact_email' => 'mike.w@example.com',
                'requires_gps_validation' => false,
                'requires_time_validation' => true,
                'check_in_start_time' => '07:30',
                'check_in_end_time' => '09:00',
                'check_out_start_time' => '15:30',
                'check_out_end_time' => '17:30',
                'max_daily_check_ins' => 1,
                'allow_multiple_check_ins' => false,
                'require_supervisor_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Pretoria Government Complex',
                'description' => 'Secure facility for government training programs',
                'address_line1' => '321 Church Street',
                'address_line2' => 'Government Precinct',
                'city' => 'Pretoria',
                'province' => 'Gauteng',
                'postal_code' => '0002',
                'country' => 'South Africa',
                'latitude' => -25.7479,
                'longitude' => 28.2293,
                'radius_meters' => 50,
                'contact_person' => 'Dr. Patricia Adams',
                'contact_phone' => '+27 12 345 6789',
                'contact_email' => 'p.adams@gov.za',
                'requires_gps_validation' => true,
                'requires_time_validation' => true,
                'check_in_start_time' => '08:30',
                'check_in_end_time' => '09:00',
                'check_out_start_time' => '16:30',
                'check_out_end_time' => '17:00',
                'max_daily_check_ins' => 1,
                'allow_multiple_check_ins' => false,
                'require_supervisor_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Port Elizabeth Industrial Site',
                'description' => 'Industrial training facility for manufacturing skills',
                'address_line1' => '987 Industrial Drive',
                'address_line2' => 'Uitenhage Industrial Park',
                'city' => 'Port Elizabeth',
                'province' => 'Eastern Cape',
                'postal_code' => '6229',
                'country' => 'South Africa',
                'latitude' => -33.9580,
                'longitude' => 25.5990,
                'radius_meters' => 300,
                'contact_person' => 'Robert Brown',
                'contact_phone' => '+27 41 234 5678',
                'contact_email' => 'r.brown@industrial.co.za',
                'requires_gps_validation' => true,
                'requires_time_validation' => false,
                'check_in_start_time' => null,
                'check_in_end_time' => null,
                'check_out_start_time' => null,
                'check_out_end_time' => null,
                'max_daily_check_ins' => 4,
                'allow_multiple_check_ins' => true,
                'require_supervisor_approval' => false,
                'is_active' => false, // Inactive for testing
            ],
            [
                'name' => 'Bloemfontein Central Office',
                'description' => 'Administrative and training office in city center',
                'address_line1' => '555 President Brand Street',
                'address_line2' => 'Suite 101',
                'city' => 'Bloemfontein',
                'province' => 'Free State',
                'postal_code' => '9300',
                'country' => 'South Africa',
                'latitude' => -29.1218,
                'longitude' => 26.2195,
                'radius_meters' => 75,
                'contact_person' => 'Linda Davis',
                'contact_phone' => '+27 51 876 5432',
                'contact_email' => 'linda.d@central.co.za',
                'requires_gps_validation' => false,
                'requires_time_validation' => false,
                'check_in_start_time' => null,
                'check_in_end_time' => null,
                'check_out_start_time' => null,
                'check_out_end_time' => null,
                'max_daily_check_ins' => 2,
                'allow_multiple_check_ins' => true,
                'require_supervisor_approval' => false,
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            $programs = $company->programs;
            
            if ($programs->isEmpty()) {
                $this->command->warn("No programs found for company: {$company->name}");
                continue;
            }

            // Create 2-3 hosts per company
            $hostCount = min(3, count($hostData));
            $companyHosts = array_slice($hostData, 0, $hostCount);
            
            foreach ($companyHosts as $index => $hostInfo) {
                $program = $programs->random(); // Assign to random program
                
                $host = Host::create([
                    'company_id' => $company->id,
                    'program_id' => $program->id,
                    'name' => $hostInfo['name'] . ' - ' . $company->name,
                    'description' => $hostInfo['description'],
                    'address_line1' => $hostInfo['address_line1'],
                    'address_line2' => $hostInfo['address_line2'],
                    'city' => $hostInfo['city'],
                    'province' => $hostInfo['province'],
                    'postal_code' => $hostInfo['postal_code'],
                    'country' => $hostInfo['country'],
                    'latitude' => $hostInfo['latitude'] + (rand(-100, 100) / 10000), // Add some variance
                    'longitude' => $hostInfo['longitude'] + (rand(-100, 100) / 10000),
                    'radius_meters' => $hostInfo['radius_meters'],
                    'contact_person' => $hostInfo['contact_person'],
                    'contact_phone' => $hostInfo['contact_phone'],
                    'contact_email' => $hostInfo['contact_email'],
                    'requires_gps_validation' => $hostInfo['requires_gps_validation'],
                    'requires_time_validation' => $hostInfo['requires_time_validation'],
                    'check_in_start_time' => $hostInfo['check_in_start_time'],
                    'check_in_end_time' => $hostInfo['check_in_end_time'],
                    'check_out_start_time' => $hostInfo['check_out_start_time'],
                    'check_out_end_time' => $hostInfo['check_out_end_time'],
                    'max_daily_check_ins' => $hostInfo['max_daily_check_ins'],
                    'allow_multiple_check_ins' => $hostInfo['allow_multiple_check_ins'],
                    'require_supervisor_approval' => $hostInfo['require_supervisor_approval'],
                    'is_active' => $hostInfo['is_active'],
                ]);

                $this->command->info("Created host: {$host->name} for program: {$program->title}");
            }
            
            // Rotate the host data for next company
            $hostData = array_slice($hostData, $hostCount) + array_slice($hostData, 0, $hostCount);
        }

        $this->command->info('Host seeder completed successfully!');
    }
}