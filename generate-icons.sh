#!/bin/bash
# Script to generate PWA icons from logo.png

echo "Generating PWA icons..."

cd /var/www/html/iso-digital/laravel-app/public

# Check if logo.jpg exists
if [ ! -f "logo.jpg" ]; then
    echo "Error: logo.jpg not found!"
    exit 1
fi

# Create icons directory if it doesn't exist
mkdir -p icons

# Generate icons using ImageMagick (if available)
if command -v convert &> /dev/null; then
    echo "Using ImageMagick to generate icons..."
    convert logo.jpg -resize 72x72 icons/icon-72x72.png
    convert logo.jpg -resize 96x96 icons/icon-96x96.png
    convert logo.jpg -resize 128x128 icons/icon-128x128.png
    convert logo.jpg -resize 144x144 icons/icon-144x144.png
    convert logo.jpg -resize 152x152 icons/icon-152x152.png
    convert logo.jpg -resize 192x192 icons/icon-192x192.png
    convert logo.jpg -resize 384x384 icons/icon-384x384.png
    convert logo.jpg -resize 512x512 icons/icon-512x512.png
    
    # Copy for iOS
    cp icons/icon-152x152.png apple-touch-icon.png
    
    echo "✅ Icons generated successfully!"
else
    # Copy logo as fallback for all sizes
    echo "ImageMagick not found, using logo.jpg as fallback for all sizes..."
    cp logo.jpg icons/icon-72x72.png
    cp logo.jpg icons/icon-96x96.png
    cp logo.jpg icons/icon-128x128.png
    cp logo.jpg icons/icon-144x144.png
    cp logo.jpg icons/icon-152x152.png
    cp logo.jpg icons/icon-192x192.png
    cp logo.jpg icons/icon-384x384.png
    cp logo.jpg icons/icon-512x512.png
    cp logo.jpg apple-touch-icon.png
    
    echo "⚠️  Icons are using original logo size (logo.jpg). Install ImageMagick for proper resizing:"
    echo "   sudo apt install imagemagick-6.q16"
fi

echo ""
echo "Icons location: /var/www/html/iso-digital/laravel-app/public/icons/"
ls -lh icons/
