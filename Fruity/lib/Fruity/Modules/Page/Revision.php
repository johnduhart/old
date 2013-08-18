<?php

namespace Fruity\Modules\Page;

use Fruity\Core;

/**
 * Represents a page revision
 *
 * @todo Needs lazyloading
 */
class Revision {

	/**
	 * The page this revision belongs to
	 *
	 * @var Page
	 */
	protected $page;

	/**
	 * Revision id
	 *
	 * @var int
	 */
	protected $revId;

	/**
	 * Parent revision
	 *
	 * Loaded on demand
	 *
	 * @var Revision
	 */
	protected $parentRev;

	/**
	 * Parent revision id
	 *
	 * @var int
	 */
	protected $parentId;

	/**
	 * Minor revision?
	 *
	 * @var bool
	 */
	protected $isMinor = false;

	/**
	 * Username
	 *
	 * @todo User object
	 * @var string
	 */
	protected $username;

	/**
	 * MW timestamp for the revision
	 *
	 * @var string
	 */
	protected $timestamp;

	/**
	 * Tags on the revision
	 *
	 * @var array
	 */
	protected $tags = array();

	/**
	 * Content of the revision
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Comment on the revision
	 *
	 * @var string
	 */
	protected $comment;

	/**
	 * @param Page $page
	 */
	public function __construct( Page $page ) {
		$this->page = $page;
	}

	/**
	 * @param array $data
	 * @param Page $page
	 * @return \Fruity\Modules\Page\Revision
	 */
	public static function newFromApiData( array $data, Page $page ) {
		$revision = new self( $page );
		$revision->loadFromApi( $data );
		return $revision;
	}

	/**
	 * Loads data from an API result
	 *
	 * @param array $data
	 */
	public function loadFromApi( array $data ) {
		static $apiProperties = array(
			'revid' => 'RevId',
			'parentid' => 'ParentId',
			'user' => 'Username',
			'timestamp' => 'Timestamp',
			'comment' => 'Comment',
			'tags' => 'tags',
			'*' => 'Content',
		);

		foreach ( $apiProperties as $prop => $func ) {
			if ( isset( $data[$prop] ) ) {
				call_user_func( array( $this, "set$func" ), $data[$prop] );
			}
		}

		// TODO: Bot?
		if ( isset( $data['minor'] ) ) {
			$this->setIsMinor( true );
		}
	}

	/**
	 * @param string $content
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setContent( $content ) {
		$this->content = $content;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param boolean $isMinor
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setIsMinor( $isMinor ) {
		$this->isMinor = $isMinor;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getIsMinor() {
		return $this->isMinor;
	}

	/**
	 * @param int $parentId
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setParentId( $parentId ) {
		$this->parentId = $parentId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->parentId;
	}

	/**
	 * @param \Fruity\Modules\Page\Revision $parentRev
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setParentRev( $parentRev ) {
		$this->parentRev = $parentRev;
		return $this;
	}

	/**
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function getParentRev() {
		return $this->parentRev;
	}

	/**
	 * @param int $revId
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setRevId( $revId ) {
		$this->revId = $revId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRevId() {
		return $this->revId;
	}

	/**
	 * @param array $tags
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setTags( $tags ) {
		$this->tags = $tags;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * @param string $timestamp
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setTimestamp( $timestamp ) {
		$this->timestamp = $timestamp;
		return $this;
	}

	/**
	 * @param int $format
	 * @return string
	 */
	public function getTimestamp( $format ) {
		return Core::formatTimestamp( $format, $this->timestamp );
	}

	/**
	 * @param string $username
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setUsername( $username ) {
		$this->username = $username;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param string $comment
	 * @return \Fruity\Modules\Page\Revision
	 */
	public function setComment( $comment ) {
		$this->comment = $comment;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getComment() {
		return $this->comment;
	}
}