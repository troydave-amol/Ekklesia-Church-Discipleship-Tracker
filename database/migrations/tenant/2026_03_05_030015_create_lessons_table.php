<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')
                  ->constrained('churches')
                  ->cascadeOnDelete();
            $table->foreignId('program_id')
                  ->constrained('programs')
                  ->cascadeOnDelete();

            $table->unsignedSmallInteger('week_number');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->index('church_id');
            $table->index('program_id');
            $table->unique(['church_id', 'program_id', 'week_number']); // no duplicate weeks per program
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
