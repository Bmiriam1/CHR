<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();
        $admin = User::where('is_employee', true)->first();

        if (!$company || !$admin) {
            $this->command->error('Company or admin user not found. Please run the main seeder first.');
            return;
        }

        // Create primary clients
        $primaryClients = [
            [
                'name' => 'Department of Education',
                'code' => 'DOE001',
                'description' => 'National Department of Education - Skills Development Programs',
                'contact_person' => 'Dr. Sarah Johnson',
                'email' => 'sarah.johnson@education.gov.za',
                'phone' => '+27 12 312 5911',
                'address' => '222 Struben Street, Pretoria',
                'city' => 'Pretoria',
                'province' => 'Gauteng',
                'postal_code' => '0001',
                'country' => 'South Africa',
            ],
            [
                'name' => 'Department of Labour',
                'code' => 'DOL001',
                'description' => 'Department of Employment and Labour - ETI Programs',
                'contact_person' => 'Mr. Thabo Mthembu',
                'email' => 'thabo.mthembu@labour.gov.za',
                'phone' => '+27 12 309 4000',
                'address' => 'Laboria House, 215 Francis Baard Street',
                'city' => 'Pretoria',
                'province' => 'Gauteng',
                'postal_code' => '0002',
                'country' => 'South Africa',
            ],
            [
                'name' => 'SETA - Services',
                'code' => 'SETA001',
                'description' => 'Services Sector Education and Training Authority',
                'contact_person' => 'Ms. Nomsa Dlamini',
                'email' => 'nomsa.dlamini@serviceseta.org.za',
                'phone' => '+27 11 276 9600',
                'address' => 'Waterfall Office Park, 74 Waterfall Drive',
                'city' => 'Midrand',
                'province' => 'Gauteng',
                'postal_code' => '1685',
                'country' => 'South Africa',
            ],
        ];

        $createdClients = [];

        foreach ($primaryClients as $clientData) {
            $client = Client::create(array_merge($clientData, [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'status' => 'active',
            ]));
            $createdClients[] = $client;
        }

        // Create sub-clients for each primary client
        $subClients = [
            // DOE sub-clients
            [
                'parent_client_id' => $createdClients[0]->id,
                'name' => 'Western Cape Education',
                'code' => 'DOE001-WC',
                'description' => 'Western Cape Provincial Education Department',
                'contact_person' => 'Ms. Lisa van der Merwe',
                'email' => 'lisa.vandermerwe@westerncape.gov.za',
                'phone' => '+27 21 467 2000',
                'address' => 'Grand Central Building, Lower Parliament Street',
                'city' => 'Cape Town',
                'province' => 'Western Cape',
                'postal_code' => '8000',
            ],
            [
                'parent_client_id' => $createdClients[0]->id,
                'name' => 'KwaZulu-Natal Education',
                'code' => 'DOE001-KZN',
                'description' => 'KwaZulu-Natal Provincial Education Department',
                'contact_person' => 'Mr. Sipho Ndlovu',
                'email' => 'sipho.ndlovu@kzneducation.gov.za',
                'phone' => '+27 33 355 0000',
                'address' => 'Pietermaritzburg, 300 Langalibalele Street',
                'city' => 'Pietermaritzburg',
                'province' => 'KwaZulu-Natal',
                'postal_code' => '3201',
            ],
            // DOL sub-clients
            [
                'parent_client_id' => $createdClients[1]->id,
                'name' => 'Gauteng Labour',
                'code' => 'DOL001-GP',
                'description' => 'Gauteng Provincial Labour Office',
                'contact_person' => 'Ms. Precious Mokoena',
                'email' => 'precious.mokoena@gauteng.gov.za',
                'phone' => '+27 11 355 5000',
                'address' => '30 Simmonds Street, Johannesburg',
                'city' => 'Johannesburg',
                'province' => 'Gauteng',
                'postal_code' => '2000',
            ],
            // SETA sub-clients
            [
                'parent_client_id' => $createdClients[2]->id,
                'name' => 'Hospitality SETA',
                'code' => 'SETA001-HOSP',
                'description' => 'Hospitality Industry Training Authority',
                'contact_person' => 'Mr. David Smith',
                'email' => 'david.smith@hospitalityseta.org.za',
                'phone' => '+27 11 217 0600',
                'address' => 'Block A, 1st Floor, 1 Park Lane, Sandton',
                'city' => 'Sandton',
                'province' => 'Gauteng',
                'postal_code' => '2196',
            ],
            [
                'parent_client_id' => $createdClients[2]->id,
                'name' => 'Tourism SETA',
                'code' => 'SETA001-TOUR',
                'description' => 'Tourism and Hospitality Training Authority',
                'contact_person' => 'Ms. Amanda Botha',
                'email' => 'amanda.botha@tourismseta.org.za',
                'phone' => '+27 11 217 0600',
                'address' => 'Block A, 1st Floor, 1 Park Lane, Sandton',
                'city' => 'Sandton',
                'province' => 'Gauteng',
                'postal_code' => '2196',
            ],
        ];

        foreach ($subClients as $subClientData) {
            Client::create(array_merge($subClientData, [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'status' => 'active',
            ]));
        }

        $this->command->info('Clients and sub-clients created successfully!');
    }
}
