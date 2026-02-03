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
        if (!Schema::hasTable('account_headers')) {
            Schema::create('account_headers', function (Blueprint $table) {
                $table->id();
                $table->enum('header_type', ['invoice', 'credit_note', 'debit_note']);
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('status')->default(true);
                $table->string('reference_number')->unique();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->index(['header_type', 'status']);
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            });
        } else {
            Schema::table('account_headers', function (Blueprint $table) {
                if (!Schema::hasColumn('account_headers', 'header_type')) {
                    $table->enum('header_type', ['invoice', 'credit_note', 'debit_note'])->default('invoice');
                }
                if (!Schema::hasColumn('account_headers', 'status')) {
                    $table->boolean('status')->default(true)->after('description');
                }
                if (!Schema::hasColumn('account_headers', 'reference_number')) {
                    $table->string('reference_number')->unique()->after('status');
                }
                if (!Schema::hasColumn('account_headers', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('reference_number');
                    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('account_headers', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                    $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_headers');
    }
};
