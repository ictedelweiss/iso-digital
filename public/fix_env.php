<?php
// Standalone Fix Environment Variables Script
// independent of Laravel framework

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
echo "Starting Standalone Environment Fix...\n";

// Try to locate .env relative to public/ (where this script is)
$possiblePaths = [
    __DIR__ . '/../.env',       // Standard Laravel
    __DIR__ . '/../../.env',    // Nested
    $_SERVER['DOCUMENT_ROOT'] . '/../.env' // Via DocRoot
];

$envFile = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $envFile = realpath($path);
        break;
    }
}

if (!$envFile) {
    echo "ERROR: Could not find .env file. Searched in:\n";
    print_r($possiblePaths);
    die();
}

echo "Found .env at: " . $envFile . "\n";

$currentEnv = file_get_contents($envFile);

// Check if AZURE_CLIENT_ID already exists
if (strpos($currentEnv, 'AZURE_CLIENT_ID') !== false) {
    echo "AZURE_CLIENT_ID already exists in .env. Skipping append.\n";
} else {
    echo "Appending Azure Configuration...\n";

    $newConfig = "\n\n" .
        "# Microsoft Azure AD for OAuth\n" .
        "AZURE_CLIENT_ID=4c3b8737-18da-4627-a039-71580f6aace6\n" .
        "AZURE_CLIENT_SECRET=kr68Q~0W3ot4dQM8B2e0wF61qcTuGTU7x12vDbbD\n" .
        "AZURE_REDIRECT_URI=https://iso-digital.eliteacademia.id/auth/microsoft/callback\n" .
        "AZURE_TENANT_ID=6d9ec31b-9635-42f8-a9cc-081a25c4efb5\n" .
        "ASSET_URL=https://iso-digital.eliteacademia.id\n" .
        "\n" .
        "# Microsoft Graph Mail Credentials\n" .
        "MS_GRAPH_TENANT_ID=6d9ec31b-9635-42f8-a9cc-081a25c4efb5\n" .
        "MS_GRAPH_CLIENT_ID=4c3b8737-18da-4627-a039-71580f6aace6\n" .
        "MS_GRAPH_CLIENT_SECRET=kr68Q~0W3ot4dQM8B2e0wF61qcTuGTU7x12vDbbD\n" .
        "MS_GRAPH_FROM_ADDRESS=\"iso.digital@edelweiss.sch.id\"\n";

    // Append to file
    if (file_put_contents($envFile, $newConfig, FILE_APPEND)) {
        echo "Configuration appended successfully.\n";
    } else {
        echo "ERROR: Failed to write to .env file (Permission denied?).\n";
    }
}

// Force MAIL_MAILER to microsoft-graph if not set or set to something else
if (preg_match('/^MAIL_MAILER=(.*)$/m', $currentEnv, $matches)) {
    echo "Current MAIL_MAILER is: " . $matches[1] . "\n";
    if (trim($matches[1]) !== 'microsoft-graph') {
        echo "Updating MAIL_MAILER to microsoft-graph...\n";
        $currentEnv = file_get_contents($envFile); // Reload
        $updatedEnv = preg_replace('/^MAIL_MAILER=.*$/m', 'MAIL_MAILER=microsoft-graph', $currentEnv);
        if (file_put_contents($envFile, $updatedEnv)) {
            echo "MAIL_MAILER updated successfully.\n";
        } else {
            echo "ERROR: Failed to update MAIL_MAILER.\n";
        }
    } else {
        echo "MAIL_MAILER is already microsoft-graph.\n";
    }
} else {
    echo "MAIL_MAILER not found. Appending...\n";
    if (file_put_contents($envFile, "\nMAIL_MAILER=microsoft-graph\n", FILE_APPEND)) {
        echo "MAIL_MAILER appended.\n";
    }
}

// Force QUEUE_CONNECTION to sync
// Without a supervisor managing queue workers on shared hosting, database queues won't process.
if (preg_match('/^QUEUE_CONNECTION=(.*)$/m', $currentEnv, $matches)) {
    echo "Current QUEUE_CONNECTION is: " . $matches[1] . "\n";
    if (trim($matches[1]) !== 'sync') {
        echo "Updating QUEUE_CONNECTION to sync (fixes non-sending emails on hosting)...\n";
        $currentEnv = file_get_contents($envFile); // Reload
        $updatedEnv = preg_replace('/^QUEUE_CONNECTION=.*$/m', 'QUEUE_CONNECTION=sync', $currentEnv);
        if (file_put_contents($envFile, $updatedEnv)) {
            echo "QUEUE_CONNECTION updated successfully.\n";
        } else {
            echo "ERROR: Failed to update QUEUE_CONNECTION.\n";
        }
    } else {
        echo "QUEUE_CONNECTION is already sync.\n";
    }
} else {
    echo "QUEUE_CONNECTION not found. Appending...\n";
    if (file_put_contents($envFile, "\nQUEUE_CONNECTION=sync\n", FILE_APPEND)) {
        echo "QUEUE_CONNECTION appended.\n";
    }
}

// Clear Config Cache by deleting the file manually
echo "Attempting to clear config cache manually...\n";
$cacheFiles = [
    dirname($envFile) . '/bootstrap/cache/config.php',
    dirname($envFile) . '/bootstrap/cache/services.php' // Older laravel
];

foreach ($cacheFiles as $cacheFile) {
    if (file_exists($cacheFile)) {
        if (unlink($cacheFile)) {
            echo "Deleted cache file: $cacheFile\n";
        } else {
            echo "WARNING: Could not delete cache file: $cacheFile (Permission denied?)\n";
        }
    } else {
        echo "Cache file not found (good): $cacheFile\n";
    }
}

echo "\nDONE. Please return to /debug-azure to verify.\n";
echo "Please DELETE this file (fix_env.php) from your server after use.\n";
echo "</pre>";
