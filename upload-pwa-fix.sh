#!/bin/bash
# Quick FTP upload for manifest and service worker fix

echo "🔧 Uploading PWA path fixes to FTP server..."
echo "Server: iso-digital.eliteacademia.id"
echo ""

# FTP Configuration
FTP_HOST="iso-digital.eliteacademia.id"
FTP_USER="aris@iso-digital.eliteacademia.id"
FTP_PASS="12345Q@zaqw"
FTP_PORT=21

echo "📦 Files to upload:"
echo "  - manifest.json (fixed paths)"
echo "  - sw.js (fixed BASE_PATH)"
echo ""

# Upload using FTP
echo "🔄 Uploading files..."
ftp -n -v $FTP_HOST $FTP_PORT << FTPEOF
user $FTP_USER $FTP_PASS
binary
prompt off

# Upload fixed PWA files
cd /public
put public/manifest.json manifest.json
put public/sw.js sw.js

bye
FTPEOF

echo ""
echo "✅ Upload completed!"
echo ""
echo "🔄 Next steps:"
echo "1. Di HP, uninstall aplikasi ISO Digital lama"
echo "2. Buka browser, clear cache & data"
echo "3. Buka: https://iso-digital.eliteacademia.id/admin"
echo "4. Install ulang PWA ke home screen"
echo "5. Sekarang seharusnya langsung ke /admin (tidak 404)"
echo ""
echo "💡 Service worker cache version diupdate ke v1.0.1"
echo "   Browser akan auto-update service worker"
