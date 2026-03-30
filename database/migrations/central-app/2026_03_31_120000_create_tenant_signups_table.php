<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_signups', function (Blueprint $table) {
            $table->id();
            $table->string('church_name');
            $table->string('church_address', 500)->nullable();
            $table->string('pastor_name');
            $table->string('pastor_email');
            $table->string('password');
            $table->string('subdomain', 32);
            $table->enum('subscription_plan', ['free', 'basic', 'standard', 'premium'])->default('free');
            $table->enum('status', ['pending', 'rejected', 'provisioned'])->default('pending');
            $table->text('rejection_note')->nullable();
            $table->string('tenant_id')->nullable()->index();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_signups');
    }
};
