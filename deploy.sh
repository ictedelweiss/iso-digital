#!/bin/bash

# ============================================
# ISO Digital - Deployment Script
# ============================================
# Script ini digunakan untuk deploy ke hosting
# Jalankan dengan: bash deploy.sh
# ============================================

set -e

echo "🚀 ISO Digital - Deployment Script"
echo "=================================="

# Optimisasi untuk production
echo ""
echo "📦 Step 1: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear old caches
echo ""
echo "🧹 Step 2: Clearing old caches..."
php artisan cache:clear

# Run migrations
echo ""
echo "📁 Step 3: Running migrations..."
php artisan migrate --force

# Link storage
echo ""
echo "🔗 Step 4: Linking storage..."
php artisan storage:link 2>/dev/null || echo "Storage already linked"

# Set permissions
echo ""
echo "🔐 Step 5: Setting permissions..."
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/storage 2>/dev/null || true

echo ""
echo "✅ Deployment complete!"
echo ""
echo "📝 PENTING: Pastikan file .env sudah dikonfigurasi dengan benar:"
echo "   DB_DATABASE=eliteac1_ISO_DIGITAL"
echo "   DB_USERNAME=eliteac1_iso_digital"
echo "   DB_PASSWORD=123Q@zaqw"
echo ""
