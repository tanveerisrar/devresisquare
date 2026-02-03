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
            $table->boolean('tax_included')->default(false)->after('due_date');
            $table->decimal('tax_rate', 6, 2)->default(0)->after('tax_included');
            $table->string('funds_goes_to')->nullable()->after('tax_rate');
            $table->string('frequency')->nullable()->after('funds_goes_to');
            $table->decimal('penalty_late_fee', 10, 2)->nullable()->after('tax_amount');
            $table->enum('commission_type', ['percent', 'flat'])->nullable()->after('penalty_late_fee');
            $table->string('commission_charged_to')->nullable()->after('commission_type');
            $table->string('commission_payable_to')->nullable()->after('commission_charged_to');
            $table->decimal('commission_value', 10, 2)->nullable()->after('commission_payable_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'tax_included',
                'tax_rate',
                'funds_goes_to',
                'frequency',
                'penalty_late_fee',
                'commission_type',
                'commission_charged_to',
                'commission_payable_to',
                'commission_value',
            ]);
        });
    }
};
