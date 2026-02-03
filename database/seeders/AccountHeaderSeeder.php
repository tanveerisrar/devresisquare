<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AccountHeader;
use Illuminate\Support\Facades\Auth;

class AccountHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            [
                'header_type' => 'invoice',
                'name' => 'Standard Invoice',
                'description' => 'Default header for general invoices',
            ],
            [
                'header_type' => 'credit_note',
                'name' => 'Customer Credit',
                'description' => 'Credits issued to customers',
            ],
            [
                'header_type' => 'debit_note',
                'name' => 'Supplier Debit',
                'description' => 'Debits raised to suppliers',
            ],
        ];

        $next = (AccountHeader::max('id') ?? 0) + 1;

        foreach ($rows as $row) {
            AccountHeader::firstOrCreate(
                ['name' => $row['name'], 'header_type' => $row['header_type']],
                array_merge($row, [
                    'status' => true,
                    'reference_number' => 'HDR-' . str_pad($next++, 6, '0', STR_PAD_LEFT),
                    'created_by' => Auth::id(),
                ])
            );
        }
    }
}
