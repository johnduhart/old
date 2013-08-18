<?php

namespace Fruity\Modules\Api\Query\P;

use Fruity\Modules\Api\Query\Base\P\BaseRevisions,
	Fruity\Modules\Api\Query\QueryParameterCollector;

/**
 * @template Limit
 * @template Properties
 */
class Revisions extends BaseRevisions {
	const PROP_IDS = 'ids';
	const PROP_FLAGS = 'flags';
	const PROP_TIMESTAMP = 'timestamp';
	const PROP_USER = 'user';
	const PROP_COMMENT = 'comment';
	const PROP_SIZE = 'size';
	const PROP_CONTENT = 'content';
	const PROP_TAGS = 'tags';

	protected $validProperties = array(
		self::PROP_IDS,
		self::PROP_FLAGS,
		self::PROP_TIMESTAMP,
		self::PROP_USER,
		self::PROP_COMMENT,
		self::PROP_SIZE,
		self::PROP_CONTENT,
		self::PROP_TAGS,
	);

	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addProp( 'revisions' )
			->setParameterIf( 'rvprop', $this->properties );

		// Limit may only be used on one page
		if ( count( $parameters->getTitles() ) == 1 ) {
			$parameters->setParameterIf( 'rvlimit', $this->getHttpLimit() );
		}
	}

}
