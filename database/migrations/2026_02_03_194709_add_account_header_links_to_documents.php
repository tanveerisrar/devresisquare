<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('account_header_id')
                ->nullable()
                ->after('user_id')
                ->constrained('account_headers')
                ->nullOnDelete();
        });

        Schema::table('credit_notes', function (Blueprint $table) {
            $table->foreignId('account_header_id')
                ->nullable()
                ->after('party_role')
                ->constrained('account_headers')
                ->nullOnDelete();
        });

        Schema::table('debit_notes', function (Blueprint $table) {
            $table->foreignId('account_header_id')
                ->nullable()
                ->after('party_role')
                ->constrained('account_headers')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['account_header_id']);
            $table->dropColumn('account_header_id');
        });

        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropForeign(['account_header_id']);
            $table->dropColumn('account_header_id');
        });

        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropForeign(['account_header_id']);
            $table->dropColumn('account_header_id');
        });
    }
};
