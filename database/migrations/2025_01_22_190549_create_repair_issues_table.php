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
        Schema::create('repair_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('repair_category_id')->constrained('repair_categories');
            $table->json('repair_navigation');
            $table->longText('description');
            $table->foreignId('tenant_id')->constrained('users');

            // Preferred availability for repair by Tenant/Owner
            $table->timestamp('tenant_availability')->nullable();

            // Access details note (rich text)
            $table->text('access_details')->nullable();

            // Add the common estimated price field.
            $table->decimal('estimated_price', 10, 2)->nullable();

            // Add a field for VAT type: either inclusive or exclusive.
            $table->enum('vat_type', ['inclusive', 'exclusive'])->nullable();

            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->string('sub_status');
            $table->string('status');
            $table->foreignId('final_contractor_id')->constrained('users');
            $table->string('reference_number', 255);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_issues');
    }
};
