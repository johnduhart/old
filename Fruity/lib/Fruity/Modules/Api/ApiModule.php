<?php

namespace Fruity\Modules\Api;

use Fruity\Module,
	Fruity\Modules\Api\ApiSelector;

class ApiModule implements Module {

	protected static $loaded = false;

	public static function initModule() {
		if ( self::$loaded ) {
			return;
		}

		self::loadQueryModules();
		self::loadActionModules();

		self::$loaded = true;
	}

	protected static function loadQueryModules() {
		$dirs = array( __DIR__ . '/Query/M', __DIR__ . '/Query/P', __DIR__ . '/Query/L' );
		$files = self::findPhpFiles( $dirs );

		$types = array(
			'M' => 'meta',
			'L' => 'list',
			'P' => 'prop',
		);

		/** @var $file \SplFileInfo */
		foreach ( $files as $file ) {
			$type = strtoupper( array_pop( explode( '/', $file->getPath() ) ) );
			$name = $file->getBasename( '.php' );
			$class = "Fruity\\Modules\\Api\\Query\\$type\\$name";

			Api::addQueryModule( $types[$type], $name, $class );
		}
	}

	protected static function loadActionModules() {
		$files = self::findPhpFiles( __DIR__ . '/Action' );

		foreach ( $files as $file ) {
			$name = $file->getBasename( '.php' );
			$class = "Fruity\\Modules\\Api\\Action\\$name";

			Api::addActionModule( $name, $class );
		}
	}

	protected static function findPhpFiles( $dirs ) {
		return \Symfony\Component\Finder\Finder::create()
			->in( $dirs )
			->files()
			->ignoreDotFiles( true )
			->ignoreVCS( true )
			->name( '*.php' );
	}
}
