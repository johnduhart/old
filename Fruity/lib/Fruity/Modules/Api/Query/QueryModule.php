<?php

namespace Fruity\Modules\Api\Query;

use Fruity\Modules\Api\Query\QueryParameterCollector,
	Fruity\Modules\Api\Query\Result;

/**
 * Interface that all query modules must implement
 */
interface QueryModule {
	/**
	 * Processes any settings and adds appropriate parameters
	 *
	 * @param QueryParameterCollector $parameters
	 * @return void
	 */
	public function execute( QueryParameterCollector $parameters );

	/**
	 * Prepares data in a form Fruity can use regardless of Api
	 *
	 * @param mixed $data
	 * @param Result $result
	 * @return void
	 */
	public function prepareResult( $data, $result );
}
