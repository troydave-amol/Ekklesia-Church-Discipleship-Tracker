<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('central_version_history', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // 'tenant', 'admin_user', etc
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('changed_by')->nullable(); // admin_users.id
            $table->text('change_description'); // What was changed
            $table->json('old_values')->nullable(); // Before state
            $table->json('new_values')->nullable(); // After state
            $table->string('action'); // 'created', 'updated', 'deleted'
            $table->timestamp('created_at');

            $table->index(['entity_type', 'entity_id']);
            $table->index('changed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('central_version_history');
    }
};
