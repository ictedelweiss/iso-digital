#!/bin/bash
# URGENT: Fix production HTTP 500 error

echo "🚨 URGENT FIX: Uploading corrected AdminPanelProvider..."
echo "Server: iso-digital.eliteacademia.id"
echo ""

FTP_HOST="iso-digital.eliteacademia.id"
FTP_USER="aris@iso-digital.eliteacademia.id"
FTP_PASS="12345Q@zaqw"
FTP_PORT=21

echo "📦 Uploading fixed file..."
ftp -n -v $FTP_HOST $FTP_PORT << FTPEOF
user $FTP_USER $FTP_PASS
binary
prompt off

cd /app/Providers/Filament
put app/Providers/Filament/AdminPanelProvider.php AdminPanelProvider.php

bye
FTPEOF

echo ""
echo "✅ Upload completed!"
echo ""
echo "🔄 Site should be back online now!"
echo "   Test: https://iso-digital.eliteacademia.id/admin"
