<?php

use Illuminate\Contracts\Console\Kernel;
use Laravel\Socialite\Facades\Socialite;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    $driver = Socialite::driver('azure');

    // Use Reflection to inspect protected properties
    $reflection = new ReflectionClass($driver);

    $clientIdProp = $reflection->getProperty('clientId');
    $clientIdProp->setAccessible(true);
    $clientId = $clientIdProp->getValue($driver);

    $clientSecretProp = $reflection->getProperty('clientSecret');
    $clientSecretProp->setAccessible(true);
    $clientSecret = $clientSecretProp->getValue($driver);

    $redirectUrlProp = $reflection->getProperty('redirectUrl');
    $redirectUrlProp->setAccessible(true);
    $redirectUrl = $redirectUrlProp->getValue($driver);

    echo "--- Debug Info ---\n";
    echo "Provider Class: " . get_class($driver) . "\n";
    echo "Client ID: " . ($clientId ?: "NULL/EMPTY") . "\n";
    echo "Client Secret: " . ($clientSecret ? "Set (" . strlen($clientSecret) . " chars)" : "NULL/EMPTY") . "\n";
    echo "Redirect URL: " . ($redirectUrl ?: "NULL/EMPTY") . "\n";

    // Check Config directly
    echo "Config services.azure.client_id: " . config('services.azure.client_id') . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
