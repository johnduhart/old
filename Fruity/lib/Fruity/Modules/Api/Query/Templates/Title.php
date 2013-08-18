<?php

class Title {
	/**
	 * Title of the category to search
	 *
	 * @var null
	 */
	protected $title = null;

	/**
	 * Set the title of the category to search though
	 *
	 * @param string $title
	 * @return Categorymembers
	 */
	public function setTitle( $title ) {
		$this->title = $title;
		return $this;
	}
}
