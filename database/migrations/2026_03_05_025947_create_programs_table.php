<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')
                  ->constrained('churches')
                  ->cascadeOnDelete();

            // Canonical name: 'Life Class', 'School of Leaders 1', etc.
            $table->string('name');
            $table->text('description')->nullable();

            // Slug key for subscription gating checks
            $table->enum('program_key', ['life_class', 'sol1', 'sol2', 'sol3'])
                  ->comment('Machine-readable key used for plan limit checks');

            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('church_id');
            $table->unique(['church_id', 'program_key']); // one per program type per church
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
