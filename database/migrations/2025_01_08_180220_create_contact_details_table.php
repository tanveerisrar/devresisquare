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
        Schema::create('contact_details', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('contact_id'); // Foreign key to contacts table
            $table->string('employment_status')->nullable(); // Employment Status
            $table->string('business_name')->nullable(); // Business Name (if applicable)
            $table->string('registered_address')->nullable(); // Business Name (if applicable)
            $table->boolean('guarantee')->nullable();
            $table->boolean('previously_rented')->nullable(); // Has previously rented?
            $table->boolean('poor_credit')->nullable(); // Poor credit history?
            $table->string('correspondence_address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('vat_number')->nullable();
            $table->boolean('allow_email')->default(false)->change();
            $table->boolean('allow_post') ->default(false)->change();
            $table->boolean('allow_text') ->default(false)->change();
            $table->boolean('allow_call') ->default(false)->change();
            $table->json('emails')->nullable();  // ["foo@a.com","bar@b.com"]
            $table->json('phones')->nullable();  // ["+1 555 1234","+1 555 5678"]

            // Common Applicant fields
            $table->decimal('budget', 10, 2)->nullable() ;
            $table->string('area')->nullable()->after('budget');
            $table->date('tentative_move_in')->nullable()->after('area');
            $table->unsignedTinyInteger('no_of_beds')->nullable()->after('tentative_move_in');
            $table->unsignedTinyInteger('no_of_tenants')->nullable()->after('no_of_beds');

            // Contractor‑specific fields
            $table->json('specialisations')->nullable()->after('no_of_tenants');
            $table->string('cover_areas')->nullable()->after('specialisations');
            $table->boolean('pi_insurance')->default(false)->after('cover_areas');
            $table->string('pi_reference_number')->nullable()->after('pi_insurance');
            $table->string('pi_certificate')->nullable()->after('pi_reference_number');

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_details');
    }
};
