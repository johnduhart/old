<?php

namespace Fruity\Modules\Api\Query\P;

use Fruity\Modules\Api\Query\Base\P\BaseInfo,
	Fruity\Modules\Api\Query\QueryParameterCollector;

/**
 * @template Properties
 */
class Info extends BaseInfo {
	const PROP_PROTECTION = 'protection';
	// TODO: More props

	protected $validProperties = array(
		self::PROP_PROTECTION,
	);

	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addProp( 'info' )
			->setParameterIf( 'inprop', $this->properties );
	}
}
