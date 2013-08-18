<?php

namespace Fruity\Modules\Api;

use Fruity\Modules\Api\ParameterCollector;

/**
 * Represents an Action
 */
interface Action {
	/**
	 * Self-explanatory
	 *
	 * @param Api $api
	 */
	public function __construct( Api $api );

	/**
	 * Executes the request, adding any parameters to it
	 *
	 * @param ParameterCollector $parameters
	 */
	public function execute( ParameterCollector $parameters );

	/**
	 * Returns data after processing the result
	 *
	 * @param array $result
	 * @return mixed
	 */
	public function processResult( array $result );
}
