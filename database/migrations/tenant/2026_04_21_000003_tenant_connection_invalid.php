<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $connection = 'mysql'; // Wrong!

    public function up(): void { }
    public function down(): void { }
};