<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tenants;

class SubscriptionPlan extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionPlanFactory> */
    protected $connection = 'mysql';

    protected $fillable = [
        'name', 'price_monthly', 'max_disciples',
        'max_leaders', 'max_programs',
        'bulk_import', 'historical_reports',
        'priority_support', 'sheets_export',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenants::class);
    }
}
