<?php

namespace Fruity\Modules\Api\Query\M;

use Fruity\Modules\Api\Query\Base\M\BaseSiteinfo,
	Fruity\Modules\Api\Query\QueryParameterCollector,
	Fruity\Modules\Api\Query\Result;

/**
 * Siteinfo query module
 *
 * https://www.mediawiki.org/wiki/API:Meta#siteinfo_.2F_si
 *
 * @template Properties
 */
class Siteinfo extends BaseSiteinfo {
	const PROP_GENERAL = 'general';
	const PROP_NAMESPACES = 'namespaces';
	const PROP_NAMESPACEALIASES = 'namespacealiases';
	// TODO: more

	protected $validProperties = array(
		self::PROP_GENERAL,
		self::PROP_NAMESPACES,
		self::PROP_NAMESPACEALIASES,
	);

	/**
	 * @param \Fruity\Modules\Api\Query\QueryParameterCollector $parameters
	 * @return void
	 */
	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addMeta( 'siteinfo' )
			->setParameterIf( 'siprop', $this->properties );
	}
}
