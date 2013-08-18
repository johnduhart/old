<?php

namespace Fruity\Modules\Api\Action;

abstract class BaseResult {

	public function __construct( array $data ) {
		$this->loadFromApi( $data );
	}

	/**
	 * Loads data passed to it into the result
	 *
	 * @param $data
	 */
	abstract protected function loadFromApi( $data );
}
