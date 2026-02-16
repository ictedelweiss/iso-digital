#!/bin/bash
# Script to upload PWA Icons to FTP server

echo "🚀 Uploading PWA Icons to FTP..."
# FTP Configuration
FTP_HOST="iso-digital.eliteacademia.id"
FTP_USER="aris@iso-digital.eliteacademia.id"
FTP_PASS="12345Q@zaqw"
FTP_PORT=21

# Upload using FTP
ftp -n -v $FTP_HOST $FTP_PORT << FTPEOF
user $FTP_USER $FTP_PASS
binary
prompt off

# Upload Icons
cd /public/icons
put public/icons/icon-72x72.png icon-72x72.png
put public/icons/icon-96x96.png icon-96x96.png
put public/icons/icon-128x128.png icon-128x128.png
put public/icons/icon-144x144.png icon-144x144.png
put public/icons/icon-152x152.png icon-152x152.png
put public/icons/icon-192x192.png icon-192x192.png
put public/icons/icon-384x384.png icon-384x384.png
put public/icons/icon-512x512.png icon-512x512.png

# Upload Apple Touch Icon
cd /public
put public/apple-touch-icon.png apple-touch-icon.png

# Upload Favicon (if different/needed)
put public/favicon.ico favicon.ico

bye
FTPEOF

echo "✅ Icon Upload completed!"
