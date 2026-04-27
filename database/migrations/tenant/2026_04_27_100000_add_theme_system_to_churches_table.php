<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'tenant';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('churches', function (Blueprint $table) {
            // Additional color options
            $table->string('secondary_color', 7)->nullable()->after('primary_color');
            $table->string('accent_color', 7)->nullable()->after('secondary_color');
            
            // Logo variants
            $table->string('logo_dark_path')->nullable()->after('logo_path');
            $table->string('favicon_path')->nullable()->after('logo_dark_path');
            
            // Theme configuration (JSON)
            // Structure: {
            //   "palette": {"primary": "#...", "shades": {...}},
            //   "typography": {"heading_font": "...", "body_font": "..."},
            //   "layout": {"sidebar_position": "left", "compact_mode": false}
            // }
            $table->json('theme_config')->nullable()->after('favicon_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('churches', function (Blueprint $table) {
            $table->dropColumn([
                'secondary_color',
                'accent_color',
                'logo_dark_path',
                'favicon_path',
                'theme_config',
            ]);
        });
    }
};
