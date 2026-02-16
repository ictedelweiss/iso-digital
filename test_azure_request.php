<?php

use Illuminate\Contracts\Console\Kernel;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Azure\Provider;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

class DebugProvider extends Provider
{
    public function debugAccessTokenResponse($code)
    {
        return $this->getAccessTokenResponse($code);
    }
}

// Manually construct the provider
$config = config('services.azure');

// Need a request instance
$request = Request::createFromGlobals();

$provider = new DebugProvider(
    $request,
    $config['client_id'],
    $config['client_secret'],
    $config['redirect']
);

// Wrap in Config object
$additionalConfig = [
    'tenant' => $config['tenant'] ?? null,
    'proxy' => null,
];
$configObject = new \SocialiteProviders\Manager\Config(
    $config['client_id'],
    $config['client_secret'],
    $config['redirect'],
    $additionalConfig
);

// Inject config for additional keys (tenant, proxy)
$provider->setConfig($configObject);

try {
    echo "Attempting request with client_id: " . $config['client_id'] . "\n";
    $response = $provider->debugAccessTokenResponse('fake_code');
    print_r($response);
} catch (\GuzzleHttp\Exception\ClientException $e) {
    echo "Client Exception: " . $e->getMessage() . "\n";
    if ($e->hasResponse()) {
        echo "Response Body: " . (string) $e->getResponse()->getBody() . "\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
