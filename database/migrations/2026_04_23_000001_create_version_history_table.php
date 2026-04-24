<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Product versioning — stores app release records in the central DB.
     * NOTE: This is NOT the same as central_version_history (which is an audit log).
     */
    public function up(): void
    {
        Schema::create('version_history', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->unique();   // e.g. "1.2.0"
            $table->string('title', 150);              // e.g. "Discipleship Tracker v1.2.0"
            $table->text('release_notes');             // Markdown-supported description
            $table->json('features')->nullable();      // ["Added leader dashboard", ...]
            $table->json('bug_fixes')->nullable();     // ["Fixed attendance bug", ...]
            $table->boolean('is_major')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('pushed_at')->nullable();    // When broadcast to tenants
            $table->unsignedBigInteger('pushed_by')->nullable(); // admin_users.id
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('version_history');
    }
};
