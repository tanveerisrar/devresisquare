<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class ContactsToUsersSeeder extends Seeder
{
    public function run()
    {
        $contacts = DB::table('contacts')->get();
        $migratedCount = 0;

        DB::transaction(function () use ($contacts, &$migratedCount) {
            foreach ($contacts as $contact) {
                // Assign dummy email if missing or duplicated
                $email = $contact->email ?: 'dummy_' . $contact->id . '@example.com';
                if (User::where('email', $email)->exists()) {
                    $email = 'duplicate_' . $contact->id . '_' . Str::random(5) . '@example.com';
                }

                $category = DB::table('contacts_categories')->find($contact->category_id);

                $user = User::create([
                    'name' => $contact->full_name ?? 'Unknown Name',
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'company_id' => null,
                    'branch_id' => null,
                    'designation_id' => null,
                    'category_id' => $contact->category_id,
                    'selected_properties' => $contact->selected_properties ?? '[]',
                    'first_name' => $contact->first_name ?? '',
                    'middle_name' => $contact->middle_name ?? '',
                    'last_name' => $contact->last_name ?? '',
                    'phone' => $contact->phone ?? 'N/A',
                    'address_line_1' => $contact->address_line_1 ?? '',
                    'address_line_2' => $contact->address_line_2 ?? '',
                    'postcode' => $contact->postcode ?? '',
                    'city' => $contact->city ?? '',
                    'country' => $contact->country ?? '',
                    'status' => $contact->status ?? 1,
                    'quick_step' => $contact->quick_step ?? null,
                    'created_by' => $contact->added_by ?? 1,
                    'updated_by' => $contact->updated_by ?? 1,
                    'can_login' => false,
                ]);

                if ($category && $category->name && Role::where('name', $category->name)->exists()) {
                    $user->assignRole($category->name);
                }

                $migratedCount++;
            }
        });

        $this->command->info("$migratedCount contacts migrated to users.");
    }
}
