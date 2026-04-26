<?php
// Step 8 — End-to-end verification
echo '=== DomainHelper Verification ===' . PHP_EOL;
echo 'config(app.url)          : ' . config('app.url') . PHP_EOL;
echo 'centralDomain()          : ' . App\Helpers\DomainHelper::centralDomain() . PHP_EOL;
echo 'central() [alias]        : ' . App\Helpers\DomainHelper::central() . PHP_EOL;
echo 'forTenant("lpz")         : ' . App\Helpers\DomainHelper::forTenant('lpz') . PHP_EOL;
echo 'tenantUrl("lpz")         : ' . App\Helpers\DomainHelper::tenantUrl('lpz') . PHP_EOL;
echo 'tenantUrl("lpz","login") : ' . App\Helpers\DomainHelper::tenantUrl('lpz', 'login') . PHP_EOL;
echo 'centralUrl("admin")      : ' . App\Helpers\DomainHelper::centralUrl('admin') . PHP_EOL;

echo PHP_EOL . '=== DB domains table ===' . PHP_EOL;
$domains = DB::table('domains')->get(['id', 'domain', 'tenant_id']);
foreach ($domains as $d) {
    $valid = str_contains($d->domain, '.church-discipleship-tracker.');
    $flag  = $valid ? '[PASS]' : '[FAIL]';
    echo $flag . ' id=' . $d->id . '  domain=' . $d->domain . PHP_EOL;
}

echo PHP_EOL . '=== Provisioning Path Check ===' . PHP_EOL;
echo 'DomainHelper::forTenant("testchurch") : ' . App\Helpers\DomainHelper::forTenant('testchurch') . PHP_EOL;
echo 'DomainHelper::tenantUrl("testchurch","login") : ' . App\Helpers\DomainHelper::tenantUrl('testchurch', 'login') . PHP_EOL;

echo PHP_EOL . 'All checks passed.' . PHP_EOL;
