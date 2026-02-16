#!/bin/bash
# Script to upload PWA changes to FTP server

echo "🚀 Uploading PWA files to FTP server..."
echo "Server: iso-digital.eliteacademia.id"
echo ""

# FTP Configuration
FTP_HOST="iso-digital.eliteacademia.id"
FTP_USER="aris@iso-digital.eliteacademia.id"
FTP_PASS="12345Q@zaqw"
FTP_PORT=21

# Create FTP command file
cat > /tmp/ftp_upload_commands.txt << 'EOF'
# Upload modified files
put DEPLOYMENT.md
put app/Providers/Filament/AdminPanelProvider.php
put generate-icons.sh
put resources/views/layouts/public.blade.php

# Create public directories if not exist
cd public
mkdir icons 2>/dev/null

# Upload PWA files
put public/manifest.json manifest.json
put public/sw.js sw.js
put public/offline.html offline.html
put public/apple-touch-icon.png apple-touch-icon.png

# Upload mobile CSS
cd css
put public/css/mobile-enhancements.css mobile-enhancements.css
cd ..

# Upload icons
cd icons
put public/icons/icon-72x72.png icon-72x72.png
put public/icons/icon-96x96.png icon-96x96.png
put public/icons/icon-128x128.png icon-128x128.png
put public/icons/icon-144x144.png icon-144x144.png
put public/icons/icon-152x152.png icon-152x152.png
put public/icons/icon-192x192.png icon-192x192.png
put public/icons/icon-384x384.png icon-384x384.png
put public/icons/icon-512x512.png icon-512x512.png
cd ..

bye
EOF

echo "📦 Files to upload:"
echo "  - DEPLOYMENT.md"
echo "  - AdminPanelProvider.php"
echo "  - public.blade.php"
echo "  - generate-icons.sh"
echo "  - manifest.json"
echo "  - sw.js"
echo "  - offline.html"
echo "  - mobile-enhancements.css"
echo "  - apple-touch-icon.png"
echo "  - 8 PWA icons"
echo ""

# Upload using FTP
echo "🔄 Uploading files..."
ftp -n -v $FTP_HOST $FTP_PORT << FTPEOF
user $FTP_USER $FTP_PASS
binary
prompt off

# Upload DEPLOYMENT.md
put DEPLOYMENT.md

# Upload AdminPanelProvider
cd /app/Providers/Filament
put app/Providers/Filament/AdminPanelProvider.php AdminPanelProvider.php

# Upload generate-icons.sh
cd /
put generate-icons.sh

# Upload public.blade.php
cd /resources/views/layouts
put resources/views/layouts/public.blade.php public.blade.php

# Upload PWA core files
cd /public
put public/manifest.json manifest.json
put public/sw.js sw.js
put public/offline.html offline.html
put public/apple-touch-icon.png apple-touch-icon.png

# Upload mobile CSS
cd /public/css
put public/css/mobile-enhancements.css mobile-enhancements.css

# Create and upload icons
cd /public
mkdir icons
cd icons
put public/icons/icon-72x72.png icon-72x72.png
put public/icons/icon-96x96.png icon-96x96.png
put public/icons/icon-128x128.png icon-128x128.png
put public/icons/icon-144x144.png icon-144x144.png
put public/icons/icon-152x152.png icon-152x152.png
put public/icons/icon-192x192.png icon-192x192.png
put public/icons/icon-384x384.png icon-384x384.png
put public/icons/icon-512x512.png icon-512x512.png

bye
FTPEOF

# Clean up
rm -f /tmp/ftp_upload_commands.txt

echo ""
echo "✅ Upload completed!"
echo ""
echo "📋 Next steps on server:"
echo "1. SSH ke server: ssh aris@iso-digital.eliteacademia.id"
echo "2. Set permissions: chmod +x generate-icons.sh"
echo "3. Clear cache: php artisan config:clear && php artisan cache:clear"
echo "4. Test PWA: buka https://iso-digital.eliteacademia.id/admin"
echo ""
echo "🔍 Verify PWA di browser:"
echo "  - Chrome DevTools → Application → Manifest"
echo "  - Chrome DevTools → Application → Service Workers"
echo "  - Test install prompt on mobile"
