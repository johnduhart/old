<?php

namespace Fruity\Modules\Api\Exceptions;

use Fruity\Modules\Api\Exceptions\ApiException;

class MissingParameter extends ApiException {
	public function __construct( $moduleName, $parameter ) {
		parent::__construct( "Module '$moduleName' is missing required parameter '$parameter'" );
	}
}
