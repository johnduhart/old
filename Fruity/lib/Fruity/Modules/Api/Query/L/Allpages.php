<?php

namespace Fruity\Modules\Api\Query\L;

use Fruity\Modules\Api\Query\Base\L\BaseAllpages,
	Fruity\Modules\Api\Query\QueryParameterCollector,
	Fruity\Modules\Api\Query\PageIterator,
	Fruity\Modules\Api\Query\Result;

/**
 * @template Limit
 */
class Allpages extends BaseAllpages {
	/**
	 * Namespace to enumerate from
	 *
	 * @var int|null
	 */
	protected $namespace = null;

	/**
	 * List pages with this prefix
	 *
	 * @var string|null
	 */
	protected $prefix = null;

	/**
	 * @param \Fruity\Modules\Api\Query\QueryParameterCollector $parameters
	 * @return void
	 */
	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addList( 'allpages' )
			->setParameterIf( 'apprefix', $this->prefix )
			->setParameterIf( 'aplimit', $this->getHttpLimit() )
			->setParameterIf( 'apnamespace', $this->namespace );
	}

	/**
	 * Called if a query-continue is present in the returned data so the module
	 * can process it and set the appropriate parameters
	 *
	 * @param array $data Data returned from the request
	 * @param \Fruity\Modules\Api\Query\ParameterCollector $parameters Parameter Collector
	 * @return void
	 */
	public function processQueryContinue( $data, $parameters ) {
		$count = count( $data['query']['allpages'] );
		if ( isset( $data['query-continue']['allpages'] )
		     && !$this->overLimit( $count )
		) {
			$parameters->setContinue( 'apfrom', $data['query-continue']['allpages']['apfrom'] );

			if ( $this->limit !== null ) {
				$parameters->setParameter( 'aplimit', ( $this->limit - $count ) );
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
			'allpages',
			PageIterator::create( $data['allpages'], $this->api->getWiki() )
		);
	}

	/**
	 * @param int|null $namespace
	 * @return Allpages
	 */
	public function setNamespace( $namespace ) {
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * @param null|string $prefix
	 * @return Allpages
	 */
	public function setPrefix( $prefix ) {
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getPrefix() {
		return $this->prefix;
	}
}