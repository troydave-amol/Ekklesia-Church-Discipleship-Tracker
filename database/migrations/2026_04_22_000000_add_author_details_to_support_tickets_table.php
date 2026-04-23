<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('central')->table('support_tickets', function (Blueprint $table) {
            if (!Schema::connection('central')->hasColumn('support_tickets', 'author_name')) {
                $table->string('author_name')->nullable()->after('user_id');
            }
            if (!Schema::connection('central')->hasColumn('support_tickets', 'author_email')) {
                $table->string('author_email')->nullable()->after('author_name');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('central')->table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'author_email']);
        });
    }
};
