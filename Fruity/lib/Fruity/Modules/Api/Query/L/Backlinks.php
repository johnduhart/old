<?php

namespace Fruity\Modules\Api\Query\L;

use Fruity\Modules\Api\Query\Base\L\BaseBacklinks,
	Fruity\Modules\Api\Query\QueryParameterCollector,
	Fruity\Modules\Api\Query\PageIterator,
	Fruity\Modules\Api\Query\Result;

/**
 * @todo Handle continue
 * @template Limit
 * @template Namespaces
 * @template Title
 */
class Backlinks extends BaseBacklinks {

	/**
	 * Mode for showing redirects
	 *
	 *    null - All
	 *    true - Only redirects
	 *    false - No redirects
	 *
	 * @var null|bool
	 */
	protected $showRedirects = null;

	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addList( 'backlinks' )
			->setParameterRequired( 'bltitle', $this->title )
			->setParameterIf( 'blnamespace', $this->getHttpNamespaces() )
			->setParameterIf( 'bllimit', $this->getHttpLimit() )
			->setParameter( 'blfilterredir', $this->getHttpShowRedirects() );
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
			'backlinks',
			PageIterator::create( $data['backlinks'], $this->api->getWiki() )
		);
	}

	protected function getHttpShowRedirects() {
		if ( $this->showRedirects === null ) {
			return 'all';
		} else {
			return $this->showRedirects ? 'redirects' : 'nonredirects';
		}
	}

	/**
	 * @param $val
	 * @return Backlinks
	 */
	public function setShowRedirects( $val ){
		$this->showRedirects = $val;
		return $this;
	}
}
