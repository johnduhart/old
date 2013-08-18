<?php

namespace Fruity\Exceptions;

/**
 * Exception class for when a configuration item is needed and not found
 */
class MissingConfigurationItem extends \Exception {
	/**
	 * @param string $item The item that is missing (eg. wiki.baseurl)
	 * @todo Add parameters to describe what the item is for
	 */
	public function __construct( $item ) {
		$message = sprintf( 'Could not find configuration item "%s"', $item );

		parent::__construct( $message );
	}
}
