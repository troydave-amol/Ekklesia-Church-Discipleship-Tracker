<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_feature_installations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('feature_update_id')->constrained('feature_updates')->cascadeOnDelete();
            $table->string('status')->default('pending'); // 'pending' | 'installing' | 'installed' | 'failed'
            $table->text('error_log')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'feature_update_id']);

            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_feature_installations');
    }
};
