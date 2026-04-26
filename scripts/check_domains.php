<?php
// Check all domain records
$domains = DB::table('domains')->get();
echo "=== DOMAINS TABLE (" . $domains->count() . " records) ===" . PHP_EOL;
foreach ($domains as $d) {
    $hasDot = str_contains($d->domain, '.');
    $status  = $hasDot ? '[OK]  ' : '[BAD] ';
    echo $status . "id={$d->id}  tenant_id={$d->tenant_id}  domain={$d->domain}" . PHP_EOL;
}

// Check tenant records
$tenants = DB::table('tenants')->get();
echo PHP_EOL . "=== TENANTS TABLE (" . $tenants->count() . " records) ===" . PHP_EOL;
foreach ($tenants as $t) {
    $data = json_decode($t->data, true) ?? [];
    echo "id={$t->id}  subdomain=" . ($data['subdomain'] ?? 'n/a') . "  domain=" . ($data['domain'] ?? 'n/a') . PHP_EOL;
}
