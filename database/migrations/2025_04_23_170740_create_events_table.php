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
            $table->string('title');
            $table->string('type')->nullable();
            $table->string('sub_type')->nullable();
            $table->string('office')->nullable();
            $table->string('status')->nullable();
            $table->string('diary_owner')->nullable();
            $table->string('on_behalf_of')->nullable();
            $table->datetime('start_datetime');
            $table->datetime('end_datetime');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('reminder')->nullable(); // e.g., 30 minutes
            $table->string('repeat')->nullable(); // daily, weekly, monthly
            $table->integer('repeat_until')->nullable(); // number of occurrences
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
