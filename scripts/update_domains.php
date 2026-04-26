<?php

$centralDomain = parse_url(config('app.url'), PHP_URL_HOST);
echo 'Central domain: ' . $centralDomain . PHP_EOL;

$domains = DB::table('domains')->get();
echo 'Found ' . $domains->count() . ' domain records.' . PHP_EOL;

foreach ($domains as $d) {
    $old  = $d->domain;
    $slug = explode('.', $old)[0];
    $new  = $slug . '.' . $centralDomain;

    if ($old !== $new) {
        DB::table('domains')->where('id', $d->id)->update(['domain' => $new]);
        echo 'Updated: ' . $old . ' => ' . $new . PHP_EOL;
    } else {
        echo 'Already correct: ' . $old . PHP_EOL;
    }
}

// Also check and update tenant data column if subdomain/domain keys exist
$tenants = DB::table('tenants')->get();
echo PHP_EOL . 'Checking ' . $tenants->count() . ' tenant records.' . PHP_EOL;

foreach ($tenants as $t) {
    $data = json_decode($t->data, true) ?? [];
    $changed = false;

    if (isset($data['subdomain']) && !isset($data['domain'])) {
        $data['domain'] = $data['subdomain'] . '.' . $centralDomain;
        $changed = true;
    }

    if ($changed) {
        DB::table('tenants')->where('id', $t->id)->update(['data' => json_encode($data)]);
        echo 'Updated tenant ' . $t->id . ' data column.' . PHP_EOL;
    }
}

echo PHP_EOL . 'Done.' . PHP_EOL;
