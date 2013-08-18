<?php

namespace Fruity\Modules\Api\Query\P;

use Fruity\Modules\Api\Query\Base\P\BaseExtlinks,
	Fruity\Modules\Api\Query\QueryParameterCollector;

/**
 * @template Limit
 */
class Extlinks extends BaseExtlinks {

	/**
	 * Processes any settings and adds appropriate parameters
	 *
	 * @param \Fruity\Modules\Api\Query\QueryParameterCollector $parameters
	 * @return void
	 */
	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addProp( 'extlinks' )
			->setParameterIf( 'ellimit', $this->getHttpLimit() );
	}
}
