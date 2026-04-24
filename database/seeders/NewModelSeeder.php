<?php

namespace Database\Seeders;

use App\Models\NewModel;
use Illuminate\Database\Seeder;

class NewModelSeeder extends Seeder
{
    public function run(): void
    {
        NewModel::factory()->count(50)->create();
    }
}