<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'id' => 'test-tenant-' . uniqid(),
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'db_name' => 'tenant_' . $this->faker->unique()->slug(2),
        ];
    }
}
