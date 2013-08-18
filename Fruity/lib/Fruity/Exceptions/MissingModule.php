<?php

namespace Fruity\Exceptions;

/**
 * Exception thrown when a module that is required can not be found
 */
class MissingModule extends \Exception {
	/**
	 * @param string $module Module name
	 */
	public function __construct( $module ) {
		parent::__construct( "A required module could not be loaded ($module)" );
	}
}
