<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devotionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')
                  ->constrained('churches')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('date');

            // true = disciple completed devotional for this day
            $table->boolean('completed')->default(false);

            // Optional: leader who verified the entry
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            // One row per disciple per day
            $table->unique(['church_id', 'user_id', 'date']);

            $table->index('church_id');
            $table->index(['church_id', 'user_id']);
            $table->index(['church_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devotionals');
    }
};
