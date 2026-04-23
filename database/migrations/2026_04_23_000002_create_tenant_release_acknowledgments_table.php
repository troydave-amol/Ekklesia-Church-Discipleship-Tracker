<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-tenant acknowledgment tracking for product releases (central DB).
     */
    public function up(): void
    {
        Schema::create('tenant_release_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_history_id')
                  ->constrained('version_history')
                  ->cascadeOnDelete();
            $table->string('tenant_id'); // References tenants.id (string UUID)
            $table->timestamp('acknowledged_at');
            $table->timestamps();

            $table->unique(['version_history_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_release_acknowledgments');
    }
};
