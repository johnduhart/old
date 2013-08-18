<?php

namespace Fruity\Modules\Page;

use Fruity\Wiki,
	Fruity\Core,
	Fruity\Modules\Api\Query\P\Revisions,
	Fruity\Modules\Template\Template;

class Page {
	/**
	 * Wiki that this page is from
	 *
	 * @var \Fruity\Wiki
	 */
	protected $wiki;

	/**
	 * Title of the page
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Title of the page without the namespace
	 *
	 * @var string
	 */
	protected $unprefixedTitle;

	/**
	 * Namespace id of the page
	 *
	 * @var int
	 */
	protected $namespaceId;

	/**
	 * PageId of the page
	 *
	 * @var int
	 */
	protected $pageId;

	/**
	 * Timestamp of when the page was last touched
	 *
	 * @var string
	 */
	protected $touchedTimestamp;

	/**
	 * Last revision ID of the page
	 *
	 * @var int
	 */
	protected $lastRevId;

	/**
	 * Page counter
	 *
	 * @var string
	 */
	protected $counter;

	/**
	 * Length of the page in bytes(?)
	 *
	 * @var int
	 */
	protected $length;

	/**
	 * Page text
	 *
	 * @var string
	 */
	protected $pageText;

	/**
	 * Array of Templates
	 *
	 * @var array
	 */
	protected $templateObjects = array();

	/**
	 * @var Revision
	 */
	protected $lastRevision;

	/**
	 * @var array
	 */
	protected $externalLinks = array();

	public function __construct( Wiki &$wiki ) {
		$this->wiki =& $wiki;
	}

	/**
	 * Creates a page from API results
	 *
	 * @param \Fruity\Wiki $wiki
	 * @param array $data
	 * @return Page
	 */
	public static function fromApiResult( Wiki $wiki, array $data ) {
		$page = new self( $wiki );
		$page->loadFromApi( $data );

		return $page;
	}

	/**
	 * Loads API results as needed
	 *
	 * @param $attribute
	 * @return bool
	 */
	protected function lazyLoad( $attribute ) {
		if ( $this->$attribute !== null ) {
			return true;
		}

		$queryBuilder = $this->wiki->getQueryBuilder();
		$queryBuilder
			->add( $queryBuilder->createProp( 'info' ) )
			->setTitles( (array) $this->getTitle() )
			->setLimit( 1 )
			->setRawMode( true );
		$data = $queryBuilder->execute();
		// TODO: Check
		list( $pageId ) = array_keys( $data['query']['pages'] );
	}

	/**
	 * Loads data from an API result
	 *
	 * @param array $data
	 */
	public function loadFromApi( array $data ) {
		static $apiProperties = array(
			'title' => 'Title',
			'ns' => 'NamespaceId',
			'pageid' => 'PageId',
			'touched' => 'TouchedTimestamp',
			'lastrevid' => 'LastRevId',
			'counter' => 'Counter',
			'length' => 'Length',
		);

		Core::loadAttributesFromArray( $this, $apiProperties, $data );

		// Load last revision
		if ( isset( $data['revisions'] )
			&& $data['revisions'][0]['revid'] == $this->getLastRevId()
		) {
			$this->lastRevision = Revision::newFromApiData( $data['revisions'][0], $this );
		}

		// External link information
		if ( isset( $data['extlinks'] ) ) {
			$this->externalLinks = array_values( $data['extlinks'] );
		}
	}

	/**
	 * @param string $title
	 * @return Page
	 */
	public function setTitle( $title ) {
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getTitle();
	}

	/**
	 * @return string
	 */
	public function getUnprefixedTitle() {
		if ( $this->unprefixedTitle !== null ) {
			return $this->unprefixedTitle;
		}

		// TODO: Canonical namespaces
		$namespaces = $this->wiki->getNamespaces();
		$namespaceLen = strlen( $namespaces[$this->getNamespaceId()]['*'] );

		$this->unprefixedTitle = substr( $this->getTitle(), $namespaceLen + 1 );
		return $this->unprefixedTitle;
	}

	/**
	 * @param int $namespaceId
	 * @return Page
	 */
	public function setNamespaceId( $namespaceId ) {
		$this->namespaceId = (int) $namespaceId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getNamespaceId() {
		$this->lazyLoad( 'namespaceId' );
		return $this->namespaceId;
	}

	/**
	 * Checks if the page is in the given namespace
	 *
	 * @param int $nsId
	 * @return bool
	 */
	public function inNamespace( $nsId ) {
		return $this->getNamespaceId() == $nsId;
	}

	/**
	 * @param int $pageId
	 * @return Page
	 */
	public function setPageId( $pageId ) {
		$this->pageId = $pageId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPageId() {
		$this->lazyLoad( 'pageId' );
		return $this->pageId;
	}

	/**
	 * @param int $lastRevId
	 * @return Page
	 */
	public function setLastRevId( $lastRevId ) {
		$this->lastRevId = $lastRevId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLastRevId() {
		$this->lazyLoad( 'lastRevId' );
		return $this->lastRevId;
	}

	/**
	 * @param string $counter
	 * @return Page
	 */
	public function setCounter( $counter ) {
		$this->counter = $counter;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCounter() {
		$this->lazyLoad( 'counter' );
		return $this->counter;
	}

	/**
	 * @param int $length
	 * @return Page
	 */
	public function setLength( $length ) {
		$this->length = $length;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLength() {
		$this->lazyLoad( 'length' );
		return $this->length;
	}

	/**
	 * @param string $touchedTimestamp
	 * @return Page
	 */
	public function setTouchedTimestamp( $touchedTimestamp ) {
		$this->touchedTimestamp = $touchedTimestamp;
		return $this;
	}

	/**
	 * @param int $format
	 * @return mixed
	 */
	public function getTouchedTimestamp( $format = FRUITY_TS_MW ) {
		$this->lazyLoad( 'touchedTimestamp' );
		return Core::formatTimestamp( $format, $this->touchedTimestamp );
	}

	/**
	 * @param array $externalLinks
	 * @return Page
	 */
	public function setExternalLinks( array $externalLinks ) {
		$this->externalLinks = $externalLinks;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getExternalLinks() {
		return $this->externalLinks;
	}

	/**
	 * Checks to see if this is a user's javscript or CSS page
	 *
	 * @return bool
	 */
	public function isUserJsOrCssPage() {
		return $this->getNamespaceId() == 2 && ( substr( $this->getTitle(), -3 ) == '.js' || substr( $this->getTitle(), -4 ) == '.css' );
	}

	/**
	 * Retrieves the page text for the page
	 *
	 * @param bool $cached
	 * @return string
	 */
	public function getText( $cached = true ) {
		if ( $cached && !empty( $this->pageText ) ) {
			return $this->pageText;
		}

		$rev = $this->getLastRevision();
		$this->pageText = $rev->getContent();

		return $this->pageText;
	}

	public function setText( $text ) {
		$this->pageText = $text;
	}

	public function _replaceRange( $replacement, $start, $stop, $callingClass = null ) {
		$text = $this->getText();

		$newText = substr_replace( $text, $replacement, $start, $stop );

		// Make sure there was a change
		if ( $newText == $text ) {
			return;
		}

		// TODO: Notify templates

		$this->setText( $newText );
	}

	/**
	 * Edits the page
	 *
	 * @todo Add editing exceptions
	 *
	 * @param string $newText
	 * @param string $summary
	 * @param bool $minor
	 * @return bool
	 */
	public function edit( $newText, $summary, $minor = true ) {
		/** @var $edit \Fruity\Modules\Api\Action\Edit */
		$edit = $this->getApi()->makeAction( 'edit' );
		$edit
			->setTitle( $this->getTitle() )
			->setText( $newText )
			->setSummary( $summary )
			->setMinor( $minor );

		/** @var $result \Fruity\Modules\Api\Action\EditResult */
		$result = $edit->run();

		// No change?
		if ( !$result->didChange() ) {
			return true;
		}

		$this->touchedTimestamp = $result->getNewTimestamp();
		$this->lastRevId = $result->getNewRevId();

		return true;
	}

	/**
	 * Returns a template object from the page
	 *
	 * @param string $templateName
	 * @return null|\Fruity\Modules\Template\Template
	 */
	public function getTemplate( $templateName ) {
		Core::requireModule( 'Template' );

		$template = Template::templateInText(
			Template::getAliases( $templateName, $this->wiki ),
			$this->getText(), $this );

		if ( $template === false ) {
			return null;
		}

		$this->templateObjects[] = $template;

		return $template;
	}

	/**
	 * Gets the last revision object
	 *
	 * @todo Wiki-level revision caching
	 *
	 * @return Revision
	 */
	public function getLastRevision() {
		if ( $this->lastRevision !== null ) {
			return $this->lastRevision;
		}

		$queryBuilder = $this->wiki->getQueryBuilder();
		/** @var \Fruity\Modules\Api\Modules\HttpApi\Query\P\Revisions $revisions  */
		$revisions = $queryBuilder->createProp( 'revisions' );
		$revisions
			->setProperties( array(
				Revisions::PROP_IDS,
				Revisions::PROP_FLAGS,
				Revisions::PROP_TIMESTAMP,
				Revisions::PROP_USER,
				Revisions::PROP_COMMENT,
				Revisions::PROP_SIZE,
				Revisions::PROP_CONTENT,
				Revisions::PROP_TAGS,
			) )
			->setLimit( 1 );
		$queryBuilder
			->add( $revisions )
			->setTitles( (array) $this->getTitle() )
			->setRawMode( true );

		$data = $queryBuilder->execute();
		$this->lastRevision = Revision::newFromApiData(
			$data['query']['pages'][$this->pageId]['revisions'][0], $this );

		return $this->lastRevision;
	}

	/**
	 * Returns an API instance
	 *
	 * @return \Fruity\Modules\Api\Api
	 */
	protected function getApi() {
		return $this->wiki->getApi();
	}
}
