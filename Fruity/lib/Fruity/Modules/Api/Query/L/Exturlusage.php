<?php

namespace Fruity\Modules\Api\Query\L;

use Fruity\Modules\Api\Query\Base\L\BaseExturlusage,
	Fruity\Modules\Api\Query\QueryParameterCollector,
	Fruity\Modules\Api\Query\PageIterator,
	Fruity\Modules\Api\Query\Result;

/**
 * @template Limit
 * @template Properties
 * @template Namespaces
 */
class Exturlusage extends BaseExturlusage {

	/**
	 * URL query to make
	 *
	 * @var string
	 */
	protected $query;

	/**
	 * Protocol to filter
	 *
	 * @var string
	 */
	protected $protocol;

	/**
	 * Processes any settings and adds appropriate parameters
	 *
	 * @param \Fruity\Modules\Api\Query\QueryParameterCollector $parameters
	 * @return void
	 */
	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addList( 'exturlusage' )
			->setParameterRequired( 'euquery', $this->query )
			->setParameterIf( 'euprotocol', $this->protocol )
			->setParameterIf( 'euprop', $this->properties )
			->setParameterIf( 'eunamespace', $this->getHttpNamespaces())
			->setParameterIf( 'eulimit', $this->getHttpLimit() );

	}

	/**
	 * Called if a query-continue is present in the returned data so the module
	 * can process it and set the appropriate parameters
	 *
	 * @param array $data Data returned from the request
	 * @param \Fruity\Modules\Api\Query\QueryParameterCollector $parameters
	 * @return void
	 */
	public function processQueryContinue( $data, $parameters ) {
		$count = count( $data['query']['exturlusage'] );
		if ( isset( $data['query-continue']['exturlusage'] )
			&& !$this->overLimit( $count )
		) {
			$parameters->setContinue( 'euoffset', $data['query-continue']['exturlusage']['euoffset'] );

			if ( $this->limit !== null ) {
				$parameters->setParameter( 'eulimit', ( $this->limit - $count ) );
			}
		}
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
			'exturlusage',
			PageIterator::create( $data['exturlusage'], $this->api->getWiki() )
		);
	}

	/**
	 * @param string $query
	 * @return Exturlusage
	 */
	public function setQuery( $query ) {
		$this->query = $query;
		return $this;
	}

	/**
	 * @param string $protocol
	 * @return Exturlusage
	 */
	public function setProtocol( $protocol ) {
		$this->protocol = $protocol;
		return $this;
	}
}
