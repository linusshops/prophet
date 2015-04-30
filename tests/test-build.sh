#!/usr/bin/env bash

composer install
git clone https://github.com/OpenMage/magento-mirror.git magento
cd magento && git checkout tags/1.9.1.0 && cd -
cp ./tests/composer.magento.json ./magento/composer.json
cd magento && composer install && cd -
cd magento && ./vendor/bin/composerCommandIntegrator.php magento-module-deploy && cd -
