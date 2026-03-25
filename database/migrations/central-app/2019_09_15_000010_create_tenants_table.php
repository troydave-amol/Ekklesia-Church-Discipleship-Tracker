<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('church_name');
            $table->string('church_address');
            $table->string('pastor_name');
            $table->string('pastor_email');
            $table->string('subdomain')->unique();
            $table->string('subscription_plan')->default('free');
            $table->string('status')->default('pending');
            $table->timestamp('subscription_started_at')->nullable();
            $table->timestamp('subscription_due_at')->nullable();
            $table->timestamps();
            $table->json('data')->nullable(); // required by stancl/tenancy
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain', 255)->unique();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
