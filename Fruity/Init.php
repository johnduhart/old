<?php

require_once( __DIR__ . '/lib/Fruity/Core.php' );
require_once( __DIR__ . '/lib/Fruity/Output.php' );

use Fruity\Core;

// Yay PHP!
date_default_timezone_set('America/New_York');

Core::setupAutoloader();
Core::loadModules(array(
	'Api',
	'User',
));
Core::enableExceptionHandler();