#!/bin/bash
# Upload mobile sidebar fixes to FTP

echo "🔧 Uploading mobile sidebar fixes..."
echo "Server: iso-digital.eliteacademia.id"
echo ""

FTP_HOST="iso-digital.eliteacademia.id"
FTP_USER="aris@iso-digital.eliteacademia.id"
FTP_PASS="12345Q@zaqw"
FTP_PORT=21

echo "📦 Files to upload:"
echo "  - mobile-enhancements.css (sidebar fixes)"
echo "  - AdminPanelProvider.php (toggle button script)"
echo ""

echo "🔄 Uploading..."
ftp -n -v $FTP_HOST $FTP_PORT << FTPEOF
user $FTP_USER $FTP_PASS
binary
prompt off

# Upload CSS
cd /public/css
put public/css/mobile-enhancements.css mobile-enhancements.css

# Upload AdminPanelProvider
cd /app/Providers/Filament
put app/Providers/Filament/AdminPanelProvider.php AdminPanelProvider.php

bye
FTPEOF

echo ""
echo "✅ Upload completed!"
echo ""
echo "🔄 Untuk apply changes:"
echo "1. SSH dan clear cache: php artisan config:clear && php artisan cache:clear"
echo "2. Refresh browser di HP (hard refresh: pull down on page)"
echo "3. Coba klik hamburger menu di kiri atas (seharusnya ada icon 3 garis)"
echo "4. Sidebar seharusnya slide dari kiri dengan overlay"
echo ""
echo "💡 Jika masih tidak muncul, coba uninstall PWA dan install ulang"
