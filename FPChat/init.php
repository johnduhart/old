<?php

declare(ticks = 1);

require_once './config.php';

// Fuck you PHP, fuck you
// TODO: Make sure the account uses UTC
date_default_timezone_set('UTC');

set_include_path(__DIR__ . '/Library/' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/StandardAutoloader.php';

$loader = new \Zend\Loader\StandardAutoloader();
$loader->registerNamespace('FPChat', 'Library/FPChat/')
		->registerNamespace('Shanty', 'Library/Shanty/')
		->register();
