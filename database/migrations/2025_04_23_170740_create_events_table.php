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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
                        // Basic fields
            $table->string('title');
            $table->string('type')->nullable();
            $table->string('sub_type')->nullable();
            $table->string('office')->nullable();
            $table->enum('status', ['Confirmed', 'Pending', 'Cancelled', 'Rescheduled'])
                  ->default('Pending');

            $table->string('diary_owner')->nullable();
            $table->string('on_behalf_of')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('reminder')->nullable(); 
            // (e.g. “30 minutes”, or store a number/unit pair if you prefer)

            // Recurrence rule
            $table->enum('repeat', ['none', 'daily', 'weekly', 'monthly'])
                  ->default('none');
            $table->integer('repeat_interval')->nullable()
                  ->default(1)
                  ->comment('Every N days/weeks/months; default 1');
            $table->integer('repeat_until_count')->nullable()
                  ->comment('Number of occurrences (excluding the original).');

            // Or, alternatively, you could store a repeat_end_date instead of a count.
            // Here we’ll use a count approach for simplicity.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
