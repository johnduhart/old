<?php

namespace Fruity\Modules\Api\Query;

/**
 * Represents a result returned by the API
 */
class Result {
	/**
	 * Raw data return by the API
	 *
	 * @var mixed
	 */
	protected $rawData;

	/**
	 * An array of iterators returned from list modules
	 *
	 * @var array
	 */
	protected $listIterators = array();

	/**
	 * Page iterator for pages returned in the query
	 *
	 * @var PageIterator
	 */
	protected $pageIterator = null;

	/**
	 * @param mixed $rawData
	 * @return Result
	 */
	public function setRawData( $rawData ) {
		$this->rawData = $rawData;
		return $this;
	}

	/**
	 * Returns raw data from the API
	 *
	 * @return mixed
	 */
	public function getRawData() {
		return $this->rawData;
	}

	/**
	 * Adds a list iterator
	 *
	 * @param $key
	 * @param $object
	 * @return Result
	 */
	public function addListIterator( $key, $object ) {
		$this->listIterators[$key] = $object;
		return $this;
	}

	/**
	 * @param $key
	 * @return \Fruity\Modules\Api\Query\PageIterator
	 */
	public function getListIterator( $key ) {
		return $this->listIterators[$key];
	}

	/**
	 * Check to see if any result blocks were registered
	 *
	 * @return int
	 */
	public function hasObjects() {
		return count( $this->listIterators ) || $this->pageIterator !== null;
	}

	/**
	 * @param \Fruity\Modules\Api\Query\PageIterator $pageIterator
	 * @return Result
	 */
	public function setPageIterator( $pageIterator ) {
		$this->pageIterator = $pageIterator;
		return $this;
	}

	/**
	 * @return \Fruity\Modules\Api\Query\PageIterator
	 */
	public function getPageIterator() {
		return $this->pageIterator;
	}
}
