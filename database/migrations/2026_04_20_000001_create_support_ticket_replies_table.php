<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('support_ticket_replies')) {
            Schema::create('support_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->nullable()->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->onDelete('cascade');
            $table->longText('message');
            $table->timestamps();
            
            $table->index('support_ticket_id');
            $table->index('user_id');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_replies');
    }
};
