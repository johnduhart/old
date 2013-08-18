<?php

namespace Fruity;

use Fruity\Core,
	Fruity\Modules\Api\Api,
	Fruity\Modules\Api\Query\L\Categorymembers,
	Fruity\Modules\Api\Query\M\Siteinfo,
	Fruity\Modules\User\User,
	Fruity\Modules\Page\Page,
	Zend\Config\Config;

/**
 * Represents a MediaWiki Wiki, or rather, a single user accessing that wiki
 */
class Wiki {
	/**
	 * Configuration object for the wiki
	 *
	 * @var \Zend\Config\Config
	 */
	protected $config;

	/**
	 * API Object
	 *
	 * @var \Fruity\Modules\Api\Api
	 */
	protected $api;

	/**
	 * Current user logged into the wiki
	 *
	 * @var \Fruity\Modules\User\User
	 */
	protected $currentUser;

	/**
	 * Array of namespaces
	 *
	 * @var array
	 */
	protected $namespaces = array();

	/**
	 * Array of namespace aliases
	 *
	 * @var array
	 */
	protected $namespaceAliases = array();

	/**
	 * Name of the MediaWiki instance (eg. Wikipedia)
	 *
	 * @var string
	 */
	protected $siteName = '';

	/**
	 * MediaWiki version (eg. MediaWiki 1.18)
	 *
	 * @var string
	 */
	protected $version = '';

	/**
	 * Server to access the wiki at (eg. //en.wikipedia.org)
	 *
	 * @var string
	 */
	protected $server = '';

	/**
	 * The host of the wiki, extracted from $server
	 *
	 * @var string
	 */
	protected $host = null;

	/**
	 * ID of the wiki
	 *
	 * @var string
	 */
	protected $wikiId = '';

	public function __construct( Config $config ) {
		$this->config = $config;
		$this->config->setReadOnly();
	}

	/**
	 * Sets the API
	 *
	 * @param \Fruity\Modules\Api\Api $api
	 */
	public function setApi( Api $api ) {
		$this->api = $api;
		$this->api->setWiki( $this );
	}

	/**
	 * Returns our API
	 *
	 * @return \Fruity\Modules\Api\Api
	 */
	public function getApi() {
		return $this->api;
	}

	/**
	 * Returns a query builder
	 *
	 * @return Modules\Api\Query\Builder
	 */
	public function getQueryBuilder() {
		return $this->api->getQueryBuilder();
	}

	/**
	 * @return void
	 */
	public function start() {
		$wikiConfig = $this->config->wiki;

		if ( $wikiConfig->autologin ) {
			$this->login();
		}

		$this->getSiteinfo();
	}

	/**
	 * Fetches information about the wiki
	 */
	public function getSiteinfo() {
		Core::requireModule( 'Api' );
		\o::info( 'Getting site info' );

		$queryBuilder = $this->getQueryBuilder();
		$siteinfo = $queryBuilder->createMeta( 'siteinfo' );
		$siteinfo->setProperties( array(
				Siteinfo::PROP_GENERAL,
				Siteinfo::PROP_NAMESPACES,
				Siteinfo::PROP_NAMESPACEALIASES,
		));
		$siteData = $queryBuilder
			->add( $siteinfo )
			->setRawMode( true )
			->execute();

		$general = $siteData['query']['general'];
		$this->siteName = $general['sitename'];
		$this->version = $general['generator'];
		$this->server = $general['server'];
		$this->wikiId = $general['wikiid'];

		$this->namespaces = $siteData['query']['namespaces'];
		$this->namespaceAliases = $siteData['query']['namespacealiases'];

		foreach ( $this->namespaces as &$namespace ) {
			$namespace['aliases'] = array();
		}

		foreach ( $this->namespaceAliases as $alias ) {
			$this->namespaces[$alias['id']]['aliases'][] = $alias['*'];
		}
	}

	/**
	 * Logs into the wiki
	 *
	 * @todo Support domains
	 *
	 * @throws \InvalidArgumentException
	 * @param null $username
	 * @param null $password
	 * @return void
	 */
	public function login( $username = null, $password = null ) {
		Core::requireModule( 'Api' );

		if ( $username === null ) {
			$username = $this->config->wiki->username;
		}
		if ( $password === null ) {
			$password = $this->config->wiki->password;
		}

		if ( $username === null || $password === null ) {
			throw new \InvalidArgumentException( 'Username and/or password is not set' );
		}

		\o::info( 'Logging in...' );
		$this->api->login( $username, $password );

		\o::info( 'Logged in, fetching info about the current user...' );
		$this->getCurrentUser();

		\o::info( 'Done.', 1 );
	}

	/**
	 * Returns the namespaces of the current wiki
	 *
	 * @return array
	 */
	public function getNamespaces() {
		return $this->namespaces;
	}

	/**
	 * Returns information about a namespace
	 *
	 * @param $namespaceId
	 * @return array
	 */
	public function getNamespace( $namespaceId ) {
		return $this->namespaces[$namespaceId];
	}

	/**
	 * Returns the current user, or creates it if it's not set
	 *
	 * @return Modules\User\User
	 */
	public function getCurrentUser() {
		Core::requireModule( 'User' );

		if ( !$this->currentUser ) {
			$this->currentUser = User::createFromCurrentUser( $this );
		}

		return $this->currentUser;
	}

	/**
	 * Returns a single page object
	 *
	 * @todo function for mulitple pages
	 *
	 * @param string $title Title of the page
	 * @return \Fruity\Modules\Page\Page
	 */
	public function getPage( $title ) {
		$this->getPages( (array) $title )->current();
	}

	/**
	 * Returns a page Iterator
	 *
	 * @param array $titles
	 * @return Modules\Api\Query\PageIterator
	 */
	public function getPages( array $titles ) {
		Core::requireModule( 'Page' );

		$queryBuilder = $this->getQueryBuilder();
		$queryBuilder
			->add( $queryBuilder->createProp( 'info' ) )
			->setTitles( $titles );
		return $queryBuilder->execute()->getPageIterator();
	}

	/**
	 * @param string $category
	 * @param null|array $namespaces
	 * @param bool $subCategories
	 * @param null $limit
	 * @return Modules\Api\Query\PageIterator
	 */
	public function getPagesInCategory( $category, $namespaces = null, $subCategories = false, $limit = null ) {
		Core::requireModule( 'Api' );
		
		$queryBuilder = $this->getQueryBuilder();
		/** @var Modules\Api\Query\L\Categorymembers $categoryMembers  */
		$categoryMembers = $queryBuilder->createList( 'categorymembers' );
		$categoryMembers
			->setProperties( Categorymembers::PROP_TITLE )
			->setTitle( $category )
			->setNamespaces( $namespaces )
			->setLimit( $limit )
			->setRecursive( $subCategories );
		$queryBuilder
			->add( $categoryMembers );

		/** @var \Fruity\Modules\Api\Query\Result $result  */
		$result = $queryBuilder->execute();
		return $result->getListIterator( 'categorymembers' );
	}

	/**
	 * Gets pages that embed $page (Like a template)
	 *
	 * @param string $page
	 * @param null|array $namespaces
	 * @param null|int $limit
	 * @return Modules\Api\Query\PageIterator
	 */
	public function getPagesThatEmbed( $page, $namespaces = null, $limit = null ) {
		Core::requireModule( 'Api' );

		$queryBuilder = $this->getQueryBuilder();
		/** @var Modules\Api\Query\L\Embeddedin $embeddedIn  */
		$embeddedIn = $queryBuilder->createList( 'embeddedin' );
		$embeddedIn
			->setTitle( $page )
			->setNamespaces( $namespaces )
			->setLimit( $limit );
		$queryBuilder->add( $embeddedIn );

		return $queryBuilder->execute()->getListIterator( 'embeddedin' );
	}

	/**
	 * @param $prefix
	 * @param null $namespace
	 * @param null $limit
	 * @return Modules\Api\Query\PageIterator
	 */
	public function getPagesWithPrefix( $prefix, $namespace = null, $limit = null ) {
		Core::requireModule( 'Api' );

		$queryBuilder = $this->getQueryBuilder();
		/** @var Modules\Api\Query\L\Allpages $allPages  */
		$allPages = $queryBuilder->createList( 'allpages' );
		$allPages
			->setPrefix( $prefix )
			->setNamespace( $namespace )
			->setLimit( $limit );
		$queryBuilder->add( $allPages );

		return $queryBuilder->execute()->getListIterator( 'allpages' );
	}

	/**
	 * @param $page
	 * @param null $namespaces
	 * @param null $limit
	 * @return Modules\Api\Query\PageIterator
	 */
	public function getPageRedirects( $page, $namespaces = null, $limit = null ) {
		Core::requireModule( 'Api' );

		$queryBuilder = $this->getQueryBuilder();
		/** @var Modules\Api\Query\L\Backlinks $backlinks  */
		$backlinks = $queryBuilder->createList( 'backlinks' );
		$backlinks
			->setTitle( $page )
			->setNamespaces( $namespaces )
			->setLimit( $limit )
			->setShowRedirects( true );
		$queryBuilder->add( $backlinks );

		return $queryBuilder->execute()->getListIterator( 'backlinks' );
	}

	/**
	 * Returns pages with an external URL matching $query
	 *
	 * @param $query
	 * @param null $namespaces
	 * @param null $limit
	 * @return Modules\Api\Query\PageIterator
	 */
	public function getPagesWithExternalUrl( $query, $namespaces = null, $limit = null ) {
		Core::requireModule( 'Api' );
		
		$queryBuilder = $this->getQueryBuilder();
		/** @var Modules\Api\Query\L\Exturlusage $extUrlUsage */
		$extUrlUsage = $queryBuilder->createList( 'exturlusage' );
		$extUrlUsage
			->setQuery( $query )
			->setNamespaces( $namespaces )
			->setLimit( $limit );
		$queryBuilder->add( $extUrlUsage );

		return $queryBuilder->execute()->getListIterator( 'exturlusage' );
	}

	/**
	 * @return string
	 */
	public function getServer() {
		return $this->server;
	}

	/**
	 * Returns the host portion of the server
	 *
	 * @return string
	 */
	public function getHost() {
		if ( $this->server == '' ) {
			return '';
		} elseif ( $this->host !== null ) {
			return $this->host;
		}

		if ( substr( $this->server, 0, 8 ) == 'https://' ) {
			$this->host = substr( $this->server, 8 );
		} elseif ( substr( $this->server, 0, 7 ) == 'http://' ) {
			$this->host = substr( $this->server, 7 );
		} elseif ( substr( $this->server, 0, 2 ) == '//' ) {
			$this->host = substr( $this->server, 2 );
		} else {
			$this->host = $this->server;
		}

		return $this->host;
	}

	/**
	 * @return string
	 */
	public function getSiteName() {
		return $this->siteName;
	}

	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function getWikiId() {
		return $this->wikiId;
	}
}
