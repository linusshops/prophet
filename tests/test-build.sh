#!/usr/bin/env bash

##When it comes time to test prophet's testing functionality, we will need to
##build an installation of magento. This script installs it from openmage,
##and then loads our fixture module.

composer install
git clone https://github.com/OpenMage/magento-mirror.git magento
cd magento && git checkout tags/1.9.1.0 && cd -
cp ./tests/composer.magento.json ./magento/composer.json
cd magento && composer install && cd -
cd magento && ./vendor/bin/composerCommandIntegrator.php magento-module-deploy && cd -
