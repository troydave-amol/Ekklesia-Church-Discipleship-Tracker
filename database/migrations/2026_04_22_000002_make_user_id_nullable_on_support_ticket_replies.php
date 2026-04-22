<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make user_id nullable so admin replies (which have admin_id instead) don't fail
        DB::connection('central')->statement(
            'ALTER TABLE `support_ticket_replies` MODIFY `user_id` BIGINT UNSIGNED NULL'
        );
    }

    public function down(): void
    {
        DB::connection('central')->statement(
            'ALTER TABLE `support_ticket_replies` MODIFY `user_id` BIGINT UNSIGNED NOT NULL'
        );
    }
};
