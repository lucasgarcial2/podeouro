#!/bin/bash
VERSION="3.0.6.0"
CODE="fastestpro-magento2-v"
OUTPUT="$(pwd)"
ROOT="$(basename "${OUTPUT}")"

# Clean unwanted files
find . -type f -name '._*' -delete
sudo find . -name '._.DS_Store' -type f -delete
sudo find . -iname '._*' -exec rm -rf {} \;

# Create a temporary directory
mkdir -p build/tmp

# Copy necessary files to the temporary directory using rsync
rsync -av --exclude='.git' --exclude='app/etc/config.php' --exclude='app/etc/env.php' --exclude='app/code/Codazon/PageBuilder' --exclude='var' --exclude='pub/static/frontend/' --exclude='pub/static/adminhtml/' --exclude='generated' --exclude='db' --exclude='build' --exclude='node_modules' --exclude='pub/media/blog/cache' --exclude='pub/media/codazon/slideshow/cache' --exclude='pub/media/codazon/amp/less/destination/*.*' --exclude='pub/media/codazon_cache' --exclude='pub/media/import' --exclude='pub/media/captcha' --exclude='pub/media/label/cache' --exclude='pub/static/_cache' --exclude='build_full.sh' --exclude='build.sh' ./ build/tmp

# Change directory to build/tmp and create the zip
cd build/tmp || exit
zip -r "${OUTPUT}/build/${CODE}${VERSION}-fullpackage.zip" ./*

# Clean up temporary directory
cd "${OUTPUT}" || exit
rm -rf build/tmp

echo "============ Created ${CODE}${VERSION} full package ============="