<?php

class Titles {
	/**
	 * Titles to query for
	 *
	 * @var array
	 */
	protected $titles;

	/**
	 * Resets the title list
	 *
	 * @return Builder
	 */
	public function clearTitles() {
		$this->titles = array();
		return $this;
	}

	/**
	 * Sets the title list to the given array
	 *
	 * @param array $titles
	 * @return Builder
	 */
	public function setTitles( array $titles ) {
		$this->clearTitles();
		$this->addTitles( $titles );
		return $this;
	}

	/**
	 * Adds an array of titles to the list
	 *
	 * @param array $titles
	 * @return Builder
	 */
	public function addTitles( array $titles ) {
		foreach ( $titles as $title ) {
			$this->addTitle( $title );
		}
		return $this;
	}

	/**
	 * Add a title to the list
	 *
	 * @param $title
	 * @return Builder
	 */
	public function addTitle( $title ) {
		$this->titles[] = $title;
		return $this;
	}
}
