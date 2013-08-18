<?php

namespace Fruity\Modules\Api\Query;

use Fruity\Modules\Page\Page,
	Fruity\Wiki;

class PageIterator implements \Countable, \Iterator {
	/**
	 * Wiki that this page is from
	 *
	 * @var \Fruity\Wiki
	 */
	protected $wiki;

	/**
	 * Array containing the pages from the result
	 *
	 * @var array
	 */
	protected $pages = array();

	/**
	 * Current position
	 *
	 * @var int
	 */
	protected $position = 0;

	/**
	 * If pages should be awoken in batches
	 *
	 * @var bool
	 */
	protected $wakePages = true;

	/**
	 * Pages that have been initialized
	 *
	 * Also, English is weird.
	 *
	 * @var array
	 */
	protected $wokenPages = array();

	/**
	 * Closure for callback queries
	 *
	 * @var \Closure
	 */
	protected $wakeQueryCallback = null;

	public function __construct( array $pages, Wiki $wiki ) {
		\Fruity\Core::requireModule( 'Page' );

		// Make sure pages have a zero-based index
		sort( $pages );

		$this->pages = $pages;
		$this->wiki = $wiki;

		return $this;
	}

	/**
	 * Returns a new PageIterator
	 *
	 * @todo Do I still need this?
	 *
	 * @param array $pages
	 * @param \Fruity\Wiki $wiki
	 * @return PageIterator
	 */
	public static function create( array $pages, Wiki &$wiki ) {
		$pi = new self( $pages, $wiki );
		return $pi;
	}

	public function merge( PageIterator $iter ) {
		$this->pages = array_merge( $this->pages, $iter->getRawPages() );
		return $this;
	}

	/**
	 * Ensures all the pages are unique
	 *
	 * @return PageIterator
	 */
	public function unique() {
		$titles = $newPages = array();

		foreach ( $this->pages as $page ) {
			if ( isset( $titles[$page['title']] ) ) {
				continue;
			}

			// Instead of just shoving it into an array I'm using to make use
			// of a key. It's faster than in_array
			$titles[$page['title']] = true;
			$newPages[] = $page;
		}

		$this->pages = $newPages;
		return $this;
	}

	protected function getRawPages() {
		return $this->pages;
	}

	/**
	 * Returns an array containing simply page titles
	 *
	 * @return array
	 */
	public function getTitles() {
		$titleArray = array();
		foreach ( $this->pages as $page ) {
			$titleArray[] = $page['title'];
		}
		return $titleArray;
	}

	/**
	 * @return int
	 */
	public function count() {
		return count( $this->pages );
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return \Fruity\Modules\Page\Page
	 */
	public function current() {
		if ( $this->awakenPages() ) {
			return $this->wokenPages[$this->position];
		}
		return Page::fromApiResult( $this->wiki, $this->pages[ $this->position ] );
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		$this->position++;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return scalar scalar on success, integer
	 * 0 on failure.
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid() {
		return isset( $this->pages[$this->position] );
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		$this->position = 0;
	}

	/**
	 * @param boolean $wakePages
	 * @return PageIterator
	 */
	public function setWakePages( $wakePages ) {
		$this->wakePages = $wakePages;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getWakePages() {
		return $this->wakePages;
	}

	protected function awakenPages() {
		if ( !$this->wakePages ) {
			return false;
		}

		// Check to see if there's still pages in queue
		if ( isset( $this->wokenPages[$this->position] ) ) {
			return true;
		}

		// Batch-initialize the pages
		$titles = array();
		$pos = $this->position;
		for ( $pos; $pos < ( $this->position + 10 ); $pos++ ) {
			if ( !isset( $this->pages[$pos] ) ) {
				break;
			}

			$titles[$this->pages[$pos]['title']] = $pos;
		}

		$queryBuilder = $this->wiki->getApi()->getQueryBuilder();
		$queryBuilder
			->setTitles( array_keys( $titles ) )
			->setRawMode( true );
		$info =  $queryBuilder->createProp( 'info' );

		if ( $this->wakeQueryCallback !== null ) {
			$f = $this->wakeQueryCallback;
			$f( $queryBuilder, &$info );
		}

		$pageData = $queryBuilder
			->add( $info )
			->execute();

		foreach ( $pageData['query']['pages'] as $pageId => $page ) {
			if ( !isset( $titles[$page['title']] ) ) {
				\o::warn( 'Invalid page name returned when waking pages', 1 );
				continue;
			}

			$this->wokenPages[$titles[$page['title']]] = Page::fromApiResult( $this->wiki, $page );
		}

		return true;
	}

	/**
	 * @param \Closure $wakeQueryCallback
	 * @return \Fruity\Modules\Api\Modules\BaseApi\Query\PageIterator
	 */
	public function setWakeQueryCallback( $wakeQueryCallback ) {
		$this->wakeQueryCallback = $wakeQueryCallback;
		return $this;
	}
}
