#!/usr/bin/env bash

composer install
git clone https://github.com/OpenMage/magento-mirror.git magento
cd magento
git checkout tags/1.9.1.0
cd ..
