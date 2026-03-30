<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Add church_id if missing
            if (!Schema::hasColumn('programs', 'church_id')) {
                $table->unsignedBigInteger('church_id')->nullable()->after('id');
            }

            // Add program_key if missing
            if (!Schema::hasColumn('programs', 'program_key')) {
                $table->string('program_key')->nullable()->after('church_id');
            }

            // Add active column to match what the app uses
            if (!Schema::hasColumn('programs', 'active')) {
                $table->boolean('active')->default(true)->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['church_id', 'program_key', 'active']);
        });
    }
};