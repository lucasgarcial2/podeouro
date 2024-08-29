#!/bin/bash
if [ -z "$1" ]; then
  echo "Usage: $0  <version>"
  exit 1
fi

# Get the version from the arguments
version=$1

# Navigate to the /magento directory
cd ..

echo "============ creating infinit ${version} theme package =============="
git archive --format zip --output build/zipfiles/infinit-m2-${version}-themepackage.zip master

cat <<EOL > build/infinit-magento2-full-package-release-note.txt
Codazon - Infinit - Magento 2.x - Full Package - version ${version}
Copyright © 2020 Codazon
Documentation: https://codazon.com/document/infinit/magento2
Changelog: https://codazon.com/document/infinit/magento2#changelog
EOL
# Add the text file to the existing zip package
zip -j "build/zipfiles/infinit-m2-${version}-themepackage.zip" build/infinit-magento2-full-package-release-note.txt