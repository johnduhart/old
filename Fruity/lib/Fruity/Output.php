<?php
/**
 * Handles output to console (errors, info messages, etc.)
 *
 * @file
 * @todo Move to module
 */

namespace Fruity {
	/**
	 * Output class, sends output to the appropriate parties
	 */
	class Output {
		const MSG_INFO = 1;
		const MSG_ERROR = 2;
		const MSG_WARN = 3;
		const MSG_DEBUG = 4;
		const MSG_USER = 5;

		/**
		 * Type labels
		 *
		 * @var array
		 */
		static protected $labels = array(
			self::MSG_INFO => 'info',
			self::MSG_ERROR => 'error',
			self::MSG_WARN => 'warn',
			self::MSG_DEBUG => 'debug',
			self::MSG_USER => 'user',
		);

		/**
		 * Custom colors for message labels
		 *
		 * @var array
		 */
		static protected $colors = array(
			self::MSG_WARN => '1;33',
			self::MSG_ERROR => '0;31',
			self::MSG_DEBUG => '1;35',
			self::MSG_USER => '1;34',
		);

		public static function out( $message, $type, $indent = 0 ) {
			// TODO: Message level checking

			for ( $i = 0; $i < $indent; $i++ ) {
				$message = '  ' . $message;
			}
			$coloredType = self::colorType( $type );
			$message = "[$coloredType]:: $message\n";

			echo $message;
		}

		/**
		 * Colorizes the type
		 *
		 * @param string $type
		 * @return string
		 */
		protected static function colorType( $type ) {
			$typeStr = str_pad( strtoupper( self::$labels[$type] ), 5, ' ' );
			if ( !Core::isWindows() && isset( self::$colors[$type] ) ) {
				return "\033[" . self::$colors[$type] . "m$typeStr\033[0m";
			}

			return $typeStr;
		}

		public static function info( $message, $indent = 0 ) {
			self::out( $message, self::MSG_INFO, $indent );
		}

		public static function error( $message, $indent = 0 ) {
			self::out( $message, self::MSG_ERROR, $indent );
		}

		public static function warn( $message, $indent = 0 ) {
			self::out( $message, self::MSG_WARN, $indent );
		}

		public static function debug( $message, $indent = 0 ) {
			//self::out( $message, self::MSG_DEBUG, $indent );
		}

		public static function user( $message, $indent = 0 ) {
			self::out( $message, self::MSG_USER, $indent );
		}

		public static function msg( $message, $indent = 0 ) {
			self::user( $message, $indent );
		}
	}
}

namespace {
	class o extends Fruity\Output {}
}