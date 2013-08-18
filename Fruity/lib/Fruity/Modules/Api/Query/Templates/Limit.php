<?php

class Limit {
	/**
	 * Limit of results to return
	 *
	 * @var null|integer
	 */
	protected $limit = null;

	/**
	 * @param int|null $limit
	 * @return BaseLimit
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
	 * Checks whether $count is over or equal to limit
	 *
	 * @param int $count
	 * @param bool $greaterThan Return false only if $count is greater
	 * than the limit
	 * @return bool
	 */
	public function overLimit( $count, $greaterThan = false ) {
		if ( $this->limit === null ) {
			return;
		}
		if ( $greaterThan ) {
			return $this->limit > $count;
		}
		return $this->limit >= $count;
	}

	/**
	 * Returns the limit in a format that works for the API
	 *
	 * @return string
	 */
	public function getHttpLimit() {
		return ( $this->limit === null ) ? 'max' : $this->limit;
	}
}
