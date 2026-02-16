#!/bin/bash
# Script to upload Environment Fix Script to FTP server

echo "🚀 Uploading Env Fix Script to FTP..."
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

# Upload fix_env.php to public root (for easy access via browser if needed) 
# Wait, usually the web root is public/, but the laravel app root is one level up.
# The user accesses the app via /iso-digital/laravel-app/
# Usually that aliases to public/. 
# Let's try to put it in public/ so it can be accessed as /fix_env.php if the alias points there.
# Based on previous context, the user accesses "https://iso-digital.eliteacademia.id".
# If I put it in `public/`, it should be accessible.

cd /public
put fix_env.php fix_env.php

bye
FTPEOF

echo "✅ Upload completed!"
