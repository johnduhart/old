<?php

namespace Fruity\Modules\Api\Query\L;

use Fruity\Modules\Api\Query\Base\L\BaseEmbeddedin,
	Fruity\Modules\Api\Query\QueryParameterCollector,
	Fruity\Modules\Api\Query\PageIterator,
	Fruity\Modules\Api\Query\Result;

/**
 * @todo Handle continue
 * @template Limit
 * @template Namespaces
 * @template Title
 */
class Embeddedin extends BaseEmbeddedin {

	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addList( 'embeddedin' )
			->setParameterRequired( 'eititle', $this->title )
			->setParameterIf( 'einamespace', $this->getHttpNamespaces() )
			->setParameterIf( 'eilimit', $this->getHttpLimit() );
	}

	/**
	 * Prepares data in a form Fruity can use regardless of Api
	 *
	 * @param mixed $data
	 * @param \Fruity\Modules\Api\Query\Result $result
	 * @return void
	 */
	public function prepareResult( $data, $result ) {
		if ( !$this->convertToQuery( $data ) ) {
			return;
		}

		$result->addListIterator(
			'embeddedin',
			PageIterator::create( $data['embeddedin'], $this->api->getWiki() )
		);
	}
}
