<?php

namespace Fruity\Modules\Api\Query;

use Fruity\Modules\Api\ParameterCollector;

class QueryParameterCollector extends ParameterCollector {
	/**
	 * Titles that are part of the query
	 *
	 * @var array
	 */
	protected $titles;

	/**
	 * Limit of results to return
	 *
	 * @var int|null
	 */
	protected $limit = false;

	/**
	 * Array of continue parameters to clear out when a request repeats
	 *
	 * @var array
	 */
	protected $continueParams = array();

	/**
	 * @param array $titles
	 * @return QueryParameterCollector
	 */
	public function setTitles( $titles ) {
		$this->titles = $titles;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getTitles() {
		return $this->titles;
	}

	/**
	 * @param int|null $limit
	 * @return QueryParameterCollector
	 */
	public function setLimit( $limit ) {
		$this->limit = $limit;
		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * Adds a module to the meta parameter
	 *
	 * @param $module
	 * @return QueryParameterCollector
	 */
	public function addMeta( $module ) {
		$this->urlParameters['meta'][] = $module;
		return $this;
	}

	/**
	 * Adds a module to the list parameter
	 *
	 * @param $module
	 * @return QueryParameterCollector
	 */
	public function addList( $module ) {
		$this->urlParameters['list'][] = $module;
		return $this;
	}

	/**
	 * Adds a module to the prop parameter
	 *
	 * @param $module
	 * @return QueryParameterCollector
	 */
	public function addProp( $module ) {
		$this->urlParameters['prop'][] = $module;
		return $this;
	}

	/**
	 * Sets a continue parameter for the API
	 *
	 * @param $param
	 * @param $value
	 * @return QueryParameterCollector
	 */
	public function setContinue( $param, $value ) {
		$this->continueParams[] = $param;
		return $this->setParameter( $param, $value );
	}

	/**
	 * Removes all continue url parameters
	 *
	 * @return QueryParameterCollector
	 */
	public function resetContinue() {
		foreach ( $this->continueParams as $param ) {
			unset( $this->urlParameters[$param] );
		}
		$this->continueParams = array();
		return $this;
	}

	/**
	 * Returns true of there are continue url parameters
	 *
	 * @return bool
	 */
	public function hasContinue() {
		return count( $this->continueParams ) > 0;
	}

	public function processParameters() {
		$this->setParameterIf( 'titles', $this->titles );
		$this->setAction( 'query' );

		$params = parent::processParameters();

		if ( $this->limit !== false ) {
			if ( $this->limit === null ) {
				$params['limit'] = 'max';
			} else {
				$params['limit'] = strval( (int) $this->limit );
			}
		}

		return $params;
	}
}
