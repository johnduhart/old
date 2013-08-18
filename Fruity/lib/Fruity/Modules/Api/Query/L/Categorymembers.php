<?php

namespace Fruity\Modules\Api\Query\L;

use Fruity\Modules\Api\Query\Base\L\BaseCategorymembers,
	Fruity\Modules\Api\Query\QueryParameterCollector,
	Fruity\Modules\Api\Query\PageIterator,
	Fruity\Modules\Api\Query\Result;

/**
 * @template Limit
 * @template Namespaces
 * @template Properties
 * @template Title
 */
class Categorymembers extends BaseCategorymembers {
	const PROP_TITLE = 'title';

	protected $validProperties = array(
		self::PROP_TITLE,
	);

	/**
	 * Should the module recurse through subcategories
	 *
	 * @var bool
	 */
	protected $recursive = false;

	/**
	 * Remove categories from the result if it's being recursed
	 *
	 * @var bool
	 */
	protected $filterCategories = false;

	/**
	 * @param \Fruity\Modules\Api\Query\QueryParameterCollector $parameters
	 * @return void
	 */
	public function execute( QueryParameterCollector $parameters ) {
		if ( $this->recursive && $this->hasNamespaces() ) {
			// Make sure to add categories to the namespace filter
			$this->addNamespace( 14 );
			$this->filterCategories = true;
		}

		$parameters
			->addList( 'categorymembers' )
			->setParameterRequired( 'cmtitle', $this->title )
			->setParameterIf( 'cmprop', $this->properties )
			->setParameterIf( 'cmlimit', $this->getHttpLimit() )
			->setParameterIf( 'cmnamespace', $this->getHttpNamespaces() );
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
		$count = count( $data['query']['categorymembers'] );
		if ( isset( $data['query-continue']['categorymembers'] )
		     && !$this->overLimit( $count )
		) {
			$parameters->setContinue( 'cmcontinue', $data['query-continue']['categorymembers']['cmcontinue'] );

			if ( $this->limit !== null ) {
				$parameters->setParameter( 'cmlimit', ( $this->limit - $count ) );
			}
		}
	}

	/**
	 * Processes the final data returned by the API
	 *
	 * @param array $data
	 * @return void
	 */
	public function processFinalData( &$realData ) {
		$data = $realData;
		if ( isset( $data['query']['categorymembers'] ) ) {
			$count = 0;
			$updateCount = function() use (&$count, $data) {
				$count = count( $data['query']['categorymembers'] );
			};
			$updateCount();

			// Do we have to recurse now?
			if ( $this->recursive && !$this->overLimit( $count ) ) {
				// Hold on to your butts
				$categories = array();
				foreach ( $data['query']['categorymembers'] as $key => $page ) {
					if ( $page['ns'] == 14 ) {
						$categories[] = $page['title'];
						if ( $this->filterCategories ) {
							unset( $data['query']['categorymembers'][$key] );
						}
					}
				}

				foreach ( $categories as $category ) {
					$queryBuilder = $this->api->getQueryBuilder();
					/** @var Modules\Api\Query\L\Categorymembers $categoryMembers  */
					$categoryMembers = $queryBuilder->createList( 'categorymembers' );
					$categoryMembers
						->setProperties( Categorymembers::PROP_TITLE )
						->setTitle( $category )
						->setRecursive( true );

					if ( $this->limit !== null ) {
						$categoryMembers->setLimit( $this->limit );
					}
					$queryBuilder
						->add( $categoryMembers )
						->setRawMode( true );

					$exec = $queryBuilder->execute();
					$data['query'] = array_merge_recursive( $data['query'], $exec['query'] );
					$updateCount();

					if ( $this->overLimit( $count ) ) {
						break;
					}
				}
			}

			// Trim it down ot meet the limit
			if ( $this->overLimit( $count, true ) ) {
				while ( $count > $this->limit ) {
					array_pop( $data['query']['categorymembers'] );
					$count--;
				}
			}

			$realData = $data;
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
			'categorymembers',
			PageIterator::create( $data['categorymembers'], $this->api->getWiki() )
		);
	}

	protected function getHttpTitle() {
		
	}

	/**
	 * Sets whether the module recurse through subcategories
	 *
	 * @param boolean $recursive
	 * @return Categorymembers
	 */
	public function setRecursive( $recursive ) {
		$this->recursive = $recursive;
		return $this;
	}
}
