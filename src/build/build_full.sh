#!/bin/bash
if [ -z "$1" ]; then
  echo "Usage: $0  <version>"
  exit 1
fi

# Get the version from the arguments
version=$1
cd ..

# Create a temporary directory
mkdir -p build/tmp

# Copy necessary files to the temporary directory using rsync
rsync -av \
    --exclude='.git' \
    --exclude='.github' \
    --exclude='.gitignore' \
    --exclude='app/etc/config.php' \
    --exclude='app/etc/env.php' \
    --include='var/.htaccess' \
    --exclude='var/**' \
    --exclude='pub/static/frontend/' \
    --exclude='pub/static/adminhtml/' \
    --include='generated/.htaccess' \
    --exclude='generated/**' \
    --exclude='db/export_db.sh' \
    --exclude='db/setup_db.sh' \
    --exclude='db/bk' \
    --exclude='db/1_schema.bk.sql' \
    --exclude='db/2_init_data.bk.sql' \
    --exclude='intro' \
    --exclude='intropage.html' \
    --exclude='build' \
    --exclude='node_modules' \
    --exclude='themesetup' \
    --exclude='pub/media/blog/cache' \
    --exclude='pub/media/catalog/product' \
    --exclude='pub/media/catalog/tmp' \
    --exclude='pub/media/codazon/slideshow/cache' \
    --exclude='pub/media/codazon/amp/less/destination/*.*' \
    --exclude='pub/media/codazon_cache' \
    --exclude='pub/media/import' \
    --exclude='pub/media/captcha' \
    --exclude='pub/media/label/cache' \
    --exclude='pub/static/_cache' \
./ build/tmp

cat <<EOL > build/tmp/infinit-magento2-full-package-release-note.txt
Codazon - Infinit - Magento 2.x - Full Package - version ${version}
Copyright © 2020 Codazon
Documentation: https://codazon.com/document/infinit/magento2
Changelog: https://codazon.com/document/infinit/magento2#changelog
EOL

# Change directory to build/tmp and create the zip
(cd build/tmp && zip -r "../../build/zipfiles/infinit-m2-${version}-fullpackage.zip" .)

# Clean up temporary directory
rm -rf build/tmp

echo "============ Created infinit-m2-${version} full package ============="