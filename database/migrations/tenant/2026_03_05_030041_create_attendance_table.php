<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')
                  ->constrained('churches')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('lesson_id')
                  ->constrained('lessons')
                  ->cascadeOnDelete();

            $table->date('date');
            $table->enum('status', ['present', 'absent'])->default('absent');

            // Who recorded this entry (leader or pastor)
            $table->foreignId('recorded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            // One record per disciple per lesson
            $table->unique(['church_id', 'user_id', 'lesson_id']);

            $table->index('church_id');
            $table->index(['church_id', 'lesson_id']);
            $table->index(['church_id', 'user_id']);
            $table->index(['church_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
