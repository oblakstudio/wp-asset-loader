#/bin/bash

NEXT_VERSION=$1
CURRENT_VERSION=$(cat composer.json | grep version | head -1 | awk -F= "{ print $2 }" | sed 's/[version:,\",]//g' | tr -d '[[:space:]]')

sed -i "s/\"version\": \"$CURRENT_VERSION\"/\"version\": \"$NEXT_VERSION\"/g" composer.json

sudo composer dump-autoload -oa

mkdir /tmp/asset-loader
cp -ar src composer.json composer.lock vendor /tmp/asset-loader 2>/dev/null
cd /tmp
zip -qr /tmp/asset-loader-$NEXT_VERSION.zip asset-loader
