<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('central')->table('support_ticket_replies', function (Blueprint $table) {
            if (!Schema::connection('central')->hasColumn('support_ticket_replies', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('central')->table('support_ticket_replies', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
    }
};
