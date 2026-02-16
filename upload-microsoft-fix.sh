#!/bin/bash
# Script to upload Microsoft Login Config to FTP server

echo "🚀 Uploading Microsoft Login Configuration to FTP server..."
echo "Server: iso-digital.eliteacademia.id"
echo ""

# FTP Configuration
FTP_HOST="iso-digital.eliteacademia.id"
FTP_USER="aris@iso-digital.eliteacademia.id"
FTP_PASS="12345Q@zaqw"
FTP_PORT=21

# Upload using FTP
echo "🔄 Uploading files..."
ftp -n -v $FTP_HOST $FTP_PORT << FTPEOF
user $FTP_USER $FTP_PASS
binary
prompt off

# Upload AppServiceProvider
cd /app/Providers
put app/Providers/AppServiceProvider.php AppServiceProvider.php

# Upload MicrosoftController
cd /app/Http/Controllers/Auth
put app/Http/Controllers/Auth/MicrosoftController.php MicrosoftController.php

# Upload Services Config
cd /config
put config/services.php services.php

# Upload Routes
cd /routes
put routes/web.php web.php

bye
FTPEOF

echo ""
echo "✅ Upload completed!"
echo ""
echo "⚠️  IMPORTANT: You MUST invoke the cache clear on the server."
echo "   Since you might not have SSH, try to access a route that clears cache if you have one,"
echo "   OR ask the server admin to run: php artisan config:clear"
echo ""
