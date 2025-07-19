<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('users_categories');
            $table->json('selected_properties')->nullable();
            $table->string('first_name', 55)->nullable();
            $table->string('middle_name', 55)->nullable();
            $table->string('last_name', 55)->nullable();
            $table->string('full_name', 155)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 55)->nullable();
            $table->string('address_line_1', 255)->nullable();
            $table->string('address_line_2', 255)->nullable();
            $table->string('postcode', 15)->nullable();
            $table->string('city', 55)->nullable();
            $table->string('country', 55)->nullable();
            $table->boolean('status')->default(1)->comment('1 for active, 0 for inactive');
            $table->integer('quick_step')->nullable();
            $table->integer('added_by')->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
