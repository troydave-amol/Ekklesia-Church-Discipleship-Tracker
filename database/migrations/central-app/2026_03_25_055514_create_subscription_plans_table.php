<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('max_disciples')->nullable();
            $table->unsignedInteger('max_leaders')->nullable();
            $table->unsignedInteger('max_programs')->nullable();
            $table->boolean('has_sheets_export')->default(false);
            $table->boolean('has_bulk_import')->default(false);
            $table->boolean('has_historical_reports')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
