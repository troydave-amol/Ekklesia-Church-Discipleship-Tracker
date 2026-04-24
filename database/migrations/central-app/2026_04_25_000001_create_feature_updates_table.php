<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_updates', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // e.g. "Prayer Requests Module"
            $table->string('slug')->unique();               // e.g. "prayer-requests"
            $table->string('version');                      // e.g. "1.2.0"
            $table->text('description')->nullable();
            $table->string('type')->default('feature');     // 'feature' | 'patch' | 'data'
            $table->string('status')->default('draft');     // 'draft' | 'published' | 'archived' | 'scaffold_failed'
            $table->json('payload')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_updates');
    }
};
