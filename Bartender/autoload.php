<?php

require_once __DIR__ . '/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces( array(
	'Symfony'    => __DIR__ . '/vendor',
	'Zend'       => __DIR__ . '/vendor/zend/library',
	'pdepend'    => __DIR__ . '/vendor/staticReflection/src/main/php',
	'Bartender'  => __DIR__ . '/src',
) );
$loader->register( true );