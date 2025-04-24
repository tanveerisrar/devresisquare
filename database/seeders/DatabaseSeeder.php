<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Call multiple seeders in one line
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            DesignationSeeder::class,
            BranchesTableSeeder::class,
            StationNamesTableSeeder::class,
            SchoolNamesTableSeeder::class,
            ReligiousPlacesTableSeeder::class,
            EstateChargesItemsSeeder::class,
            EstateChargesSeeder::class,
            OwnerGroupSeeder::class,
            ContactCategorySeeder::class,
            ContactsSeeder::class,
            CurrencySeeder::class,
            PropertiesTableSeeder::class,
            PropertyResponsibilitySeeder::class,
            ComplianceTypeSeeder::class,
            JobTypesSeeder::class,
            LocalAuthoritySeeder::class,
        ]);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
