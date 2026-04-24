<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::connection('central')->hasTable('support_tickets')) {
            Schema::connection('central')->create('support_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('subject');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('description');
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
