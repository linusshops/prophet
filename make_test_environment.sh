#!/usr/bin/env bash

git clone --branch magento-1.9 --depth 1 https://github.com/OpenMage/magento-mirror.git testbed
cp ./tests/composer.magento.json ./testbed/composer.json
cp ./tests/prophet.magento.yml ./testbed/prophet.yml
cd testbed && composer install && composer run-script post-install-cmd -vvv -- --redeploy && cd -
