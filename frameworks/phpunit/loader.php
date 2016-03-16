<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-16
 */

use LinusShops\Prophet\Events;
use LinusShops\Prophet\Magento;

$frameworkPath = __DIR__;
$prophetRoot = $argv[1];
$modulePath = $argv[2];
$magentoPath = $argv[3];

//Local autoloader
require($frameworkPath.'/vendor/autoload.php');

//Prophet autoloader
require($prophetRoot.'/vendor/autoload.php');

$options = new \LinusShops\Prophet\Events\Options();

Events::dispatch(Events::PROPHET_PREMAGENTO, $options);
Magento::bootstrap($options);
Events::dispatch(Events::PROPHET_POSTMAGENTO);

Magento::injectAutoloaders($modulePath, $magentoPath);
