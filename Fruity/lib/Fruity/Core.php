<?php

namespace Fruity;

define( 'FRUITY_TS_MW', 0 );
define( 'FRUITY_TS_UNIX', 1 );

use Fruity\Exceptions\MissingModule,
	Fruity\Modules\Api\Api,
	Symfony\Component\ClassLoader\UniversalClassLoader;

/**
 * Core of Fruity
 */
class Core {
	/**
	 * Array of modules loaded
	 *
	 * @var array
	 */
	protected static $loadedModules = array();

	/**
	 * Autoloader class
	 *
	 * @var \Symfony\Component\ClassLoader\UniversalClassLoader
	 */
	protected static $autoloader = null;

	/**
	 * No initialization
	 */
	private final function __construct() {}

	/**
	 * Sets up the autoloader
	 *
	 * @return void
	 */
	public static function setupAutoloader() {
		if ( self::$autoloader !== null ) {
			return;
		}

		require_once( __DIR__ . '/../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php' );

		$loader = new UniversalClassLoader();
		$loader->registerNamespaces(array(
			'Fruity' => __DIR__ . '/../',
			'Symfony' => __DIR__ . '/../vendor/',
			'Zend' => __DIR__ . '/../vendor/zend/library/'
		));
		$loader->register();

		self::$autoloader = $loader;
	}

	/**
	 * Returns the autoloader instance
	 *
	 * @return \Symfony\Component\ClassLoader\UniversalClassLoader
	 */
	public static function getAutoloader() {
		return self::$autoloader;
	}

	/**
	 * Loads an array of modules
	 *
	 * @see Core::loadModule
	 * @param array $modules Modules to load
	 * @return void
	 */
	public static function loadModules( array $modules ) {
		foreach ( $modules as $module ) {
			self::loadModule( $module );
		}
	}

	/**
	 * Loads the given module and returns false if it cannot be loaded
	 *
	 * @param string $module Name of the module
	 * @return bool
	 */
	public static function loadModule( $module ) {
		if ( self::moduleLoaded( $module ) ) {
			return true;
		}

		$class = "Fruity\\Modules\\$module\\{$module}Module";
		if ( class_exists( $class ) && self::doesImplement( $class, 'Fruity\\Module' ) ) {
			call_user_func( array( $class, 'initModule' ) );
			self::$loadedModules[$module] = $class;
			return true;
		}

		return false;
	}

	/**
	 * Returns whether $module is loaded
	 *
	 * @param string $module Module name
	 * @return bool TRUE if the module is loaded
	 */
	public static function moduleLoaded( $module ) {
		return isset( self::$loadedModules[$module] );
	}

	/**
	 * Ensures that a module is loaded, otherwise throws an exception if
	 * it cannot be found
	 *
	 * @throws Exceptions\MissingModule
	 * @param string $module Module name
	 * @return bool
	 */
	public static function requireModule( $module ) {
		if ( self::moduleLoaded( $module ) ) {
			return true;
		}

		if ( !self::loadModule( $module ) ) {
			throw new MissingModule( $module );
		}

		return true;
	}

	/**
	 * @throws \InvalidArgumentException
	 * @param $configFile
	 * @return Wiki
	 */
	public static function initWiki( $configFile ) {
		$config = self::loadConfiguration( $configFile );

		return self::initWikiFromConfig( $config );
	}

	public static function initWikiFromConfig( \Zend\Config\Config $config ) {
		$wiki = new Wiki( $config );

		if ( self::moduleLoaded( 'Api' ) ) {
			// Get an API for the wiki
			$wiki->setApi( new Api( $config ) );
		}

		$wiki->start();

		return $wiki;
	}

	/**
	 * Runs a robot
	 *
	 * @param string $botClass
	 * @param string $configFile
	 * @throws \InvalidArgumentException
	 */
	public static function runBot( $botClass, $configFile ) {
		if ( !is_subclass_of( $botClass, '\\Fruity\\Bot' ) ) {
			throw new \InvalidArgumentException( 'Bot class passed to ' . __METHOD__ . ' is not a child of Bot' );
		}

		$bot = new $botClass( self::initWiki( $configFile ) );
		$bot->run();
	}

	/**
	 * Loads configuration files
	 *
	 * @param string $fileName Location of the file to load
	 * @return \Zend\Config\Config
	 * @todo This needs cleaning
	 */
	public static function loadConfiguration( $fileName ) {
		if ( !is_readable( $fileName ) ) {
			throw new \InvalidArgumentException( 'Invalid configuration file passed to ' . __METHOD__ );
		}

		$extension = strtolower( array_pop( explode( '.', $fileName ) ) );
		switch ( $extension ) {
			case 'yml':
			case 'yaml':
				// The ability to mix and match is awesome :)
				$config = new \Zend\Config\Yaml( $fileName, null,
					array( 'yaml_decoder' => array( 'Symfony\\Component\\Yaml\\Yaml', 'parse' ), 'allow_modifications' => true ) );
				break;

			case 'xml':
			case 'ini':
			case 'json':
				$class = '\\Zend\\Config\\' . ucfirst( $extension );
				$config = new $class( $fileName, null, array( 'allow_modifications' => true ) );
				break;

			default:
				throw new \InvalidArgumentException( 'Unsupported configuration file passed to ' . $extension . __METHOD__ );
		}

		// TODO: Imports

		return $config;
	}

	/**
	 * Outputs unhandled exception information to the console
	 *
	 * @todo Make this a bit less icky
	 * 
	 * @param \Exception $exception The exception
	 * @return void
	 */
	public static function handleException( \Exception $exception ) {
		$red = "\033[0;31m";
		$redBold = "\033[1;31m";
		$reset = "\033[0m";

		if ( Core::isWindows() ) {
			$red = $redBold = $reset = '';
		}

		$msg = <<<MSG

{$red}############################################
### An unhandled exception has occurred! ###
############################################{$reset}

{$redBold}Message:{$reset} %s
{$redBold}File:{$reset} %s
{$redBold}Line:{$reset} %d
{$redBold}Backtrace:{$reset}
%s

{$red}############################################
###### Program execution has stopped #######
############################################{$reset}

MSG;
		printf( $msg, $exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTraceAsString() );
	}

	/**
	 * Enables our exception handler
	 *
	 * @see \Fruity\Core::handleException
	 * @return void
	 */
	public static function enableExceptionHandler() {
		set_exception_handler( array( 'Fruity\\Core', 'handleException' ) );
	}

	/**
	 * Disables our exception handler
	 *
	 * @see \Fruity\Core::handleException
	 * @return void
	 */
	public static function disableExceptionHandler() {
		restore_exception_handler();
	}

	/**
	 * Checks to see if a class implements an interface
	 *
	 * @param string $class
	 * @param string $interface
	 * @return bool
	 */
	public static function doesImplement( $class, $interface ) {
		return in_array( $interface, class_implements( $class ) );
	}

	/**
	 * Checks to see if the current OS is windows
	 *
	 * @return bool
	 */
	public static function isWindows() {
		static $isWindows = null;
		if ( $isWindows === null ) {
			$isWindows = ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' );
		}
		return $isWindows;
	}

	/**
	 * Formats timestamps
	 *
	 * @param $format
	 * @param $timestamp
	 * @return int
	 */
	public static function formatTimestamp( $format, $timestamp ) {
		switch ( $format ) {
			case FRUITY_TS_UNIX:
				return strtotime( $timestamp );
			case FRUITY_TS_MW:
			default:
				return $timestamp;
		}
	}

	/**
	 * Sets object attributes from an array (like an API result)
	 *
	 * @todo Add smart function adding
	 *
	 * @param $object
	 * @param array $mapping
	 * @param array $data
	 */
	public static function loadAttributesFromArray( $object, array $mapping, array $data ) {
		foreach ( $mapping as $prop => $func ) {
			if ( isset( $data[$prop] ) ) {
				call_user_func( array( $object, "set$func" ), $data[$prop] );
			}
		}
	}
}
