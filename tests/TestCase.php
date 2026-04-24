<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        
        if (app()->bound('tenancy')) {
            try {
                tenancy()->end();
            } catch (\Throwable $e) {
                // Already ended or not started — fine
            }
        }
    }
}
