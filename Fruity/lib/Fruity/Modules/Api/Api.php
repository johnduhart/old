<?php

namespace Fruity\Modules\Api;

use Fruity\Wiki,
	Fruity\Exceptions\MissingConfigurationItem,
	Fruity\Modules\Api\Exceptions\LoginException,
	Fruity\Modules\Api\Exceptions\ApiError,
	Zend\Config\Config,
	Zend\Http\Client,
	Zend\Http\Request;

/**
 * HTTP API used to access api.php
 */
class Api {
	/**
	 * Wiki Object
	 *
	 * @var \Fruity\Wiki
	 */
	protected $wiki;

	/**
	 * Is the user logged in with this API object?
	 *
	 * @var bool
	 */
	protected $authenticated = false;

	/**
	 * Default options for editing
	 *
	 * @var array
	 */
	protected static $editDefaults = array(
		'minor' => false,
		'bot' => true,
		'summary' => '',
		'watchlist' => 'preferences',
	);

	/**
	 * Number of HTTP requests made
	 *
	 * @var int
	 */
	protected static $httpRequests = 0;

	/**
	 * HTTP Client to accessing the API
	 *
	 * @var \Zend\Http\Client
	 */
	public $http;

	/**
	 * Array of tokens
	 *
	 * @var array
	 */
	protected $tokens = array();

	/**
	 * Timestamp of when the next request can be made
	 *
	 * @var int
	 */
	static protected $nextRequest = 0;

	/**
	 * Array of action classes
	 *
	 * @var array
	 */
	static protected $actionClasses = array();

	public function __construct( Config $config ) {
		if ( !isset( $config->wiki->baseurl ) ) {
			throw new MissingConfigurationItem( 'wiki.baseurl' );
		}

		// Note: Zend's cURL adapter is buggy as shit. Don't use it.
		// WTF? Didn't I just finish telling you NOT to use CURL? YEP.
		// Something is wrong with Zend's cookie storage causing sessions
		// not to stick. This works, just leave it for now...
		$cookieJar = sys_get_temp_dir() . '/fruity.cookies.' . md5( time() . rand( 0, 999 ) ) . '.dat';
		$this->http = new Client(null, array(
			//'adapter' => 'Zend\Http\Client\Adapter\Curl',
			'curloptions' => array(
				CURLOPT_USERAGENT => 'Fruity',
				CURLOPT_COOKIEJAR => $cookieJar,
				CURLOPT_COOKIEFILE => $cookieJar,
			),
		));
		$this->http->setUri( $config->wiki->baseurl );
	}

	/**
	 * Adds a wiki object to the API
	 *
	 * @param \Fruity\Wiki $wiki
	 * @return void
	 */
	public function setWiki( Wiki &$wiki ) {
		$this->wiki =& $wiki;
	}

	/**
	 * Returns the wiki instance
	 *
	 * @return \Fruity\Wiki
	 */
	public function getWiki() {
		return $this->wiki;
	}

	/**
	 * @deprecated
	 * @return string
	 */
	public static function getType() {
		return 'http';
	}

	/**
	 * @deprecated
	 * @return string
	 */
	public function getNamespace() {
		return 'Fruity\\Modules\\Api';
	}

	/**
	 * @return Query\Builder
	 */
	public function getQueryBuilder() {
		return new Query\Builder( $this );
	}

	/**
	 * Adds a query module
	 *
	 * @param $type
	 * @param $name
	 * @param $class
	 */
	public static function addQueryModule( $type, $name, $class ) {
		Query\Builder::addQueryModule( $type, $name, $class );
	}

	/**
	 * Adds an action module
	 *
	 * @param $name
	 * @param $class
	 */
	public static function addActionModule( $name, $class ) {
		$name = strtolower( $name );
		self::$actionClasses[$name] = $class;
	}

	/**
	 * Logs in to the wiki
	 *
	 * @todo Convert to Action
	 *
	 * @param $username
	 * @param $password
	 * @return bool
	 * @throws Exceptions\LoginException|\RuntimeException
	 */
	public function login( $username, $password ) {
		$params = array(
			'action' => 'login',
			'lgname' => $username,
			'lgpassword' => $password,
		);
		$tries = 0;

		// Make an initial request to fetch the token
		// then repeat it with the token
		do {
			if ( ++$tries > 3 ) {
				throw new LoginException( '_MaxRetry' );
			}

			$data = $this->makeRequest( $params, true );

			if ( !isset( $data['login']['result'] ) ) {
				throw new \RuntimeException( 'Missing login result' );
			}

			switch ( $data['login']['result'] ) {
				case 'NeedToken':
					$params['lgtoken'] = $data['login']['token'];
					continue;
					
				case 'Throttled':
					$wait = $data['login']['wait'];
					\o::info( "Login attempt throttled, waiting $wait seconds...", 1 );
					sleep($wait);
					continue;

				case 'Success':
					$this->http->addCookie(
						$data['login']['cookieprefix'] . 'Token',
						$data['login']['lgtoken'] );
					break 2;

				/* Errors */
				default:
					throw new LoginException( $data['login']['result'] );
			}
		} while (true);

		$this->authenticated = true;

		return true;
	}

	/**
	 * Edits a page on the wiki
	 *
	 * @param string $title
	 * @param string $text
	 * @param array $options
	 * @deprecated
	 * @return array
	 */
	public function edit( $title, $text, array $options = array() ) {
		throw new \Exception( __METHOD__ . " is deprecated" );
	}

	/**
	 * Creates an action
	 *
	 * @param $action
	 * @return Action
	 */
	public function makeAction( $action ) {
		$action = strtolower( $action );
		$action = new self::$actionClasses[$action]( $this );
		return $action;
	}

	/**
	 * Runs an action
	 *
	 * @todo Allow for action to take complete control via PC
	 *
	 * @param Action $action
	 * @return mixed
	 */
	public function runAction( Action $action ) {
		$parameters = new ParameterCollector();
		$action->execute( $parameters );

		$result = $this->makeRequest( $parameters->processParameters(), true );

		return $action->processResult( $result );
	}

	/**
	 * Returns a token
	 *
	 * @param string $token
	 * @return string
	 */
	public function getToken( $token = 'edit' ) {
		if ( isset( $this->tokens[$token] ) ) {
			return $this->tokens[$token];
		}

		// Check to see if we have any tokens at all
		$getTokens = array( $token );
		if ( !count( $this->tokens ) ) {
			$getTokens = array(
				'edit',
				'delete',
				'protect',
				'move',
				'block',
				'unblock',
				'email',
				'import',
				'watch',
			);
		}

		$tokenParams = array(
			'action' => 'query',
			'prop' => 'info',
			'titles' => 'SomePage', // A title name is needed here
			'intoken' => implode( '|', $getTokens ),
			'indexpageids' => 'yes',
		);
		\o::info( 'Retrieving tokens ' . implode( ', ', $getTokens ), 1 );
		$data = $this->makeRequest( $tokenParams );

		$pageId = $data['query']['pageids'][0];
		$page = $data['query']['pages'][$pageId];
		foreach ( $getTokens as $t ) {
			if ( isset( $page[$t . 'token'] ) ) {
				$this->tokens[$t] = $page[$t . 'token'];
			} else {
				$this->tokens[$t] = null;
			}
		}

		return $this->tokens[$token];
	}

	/**
	 * Makes a request to api.php
	 *
	 * @throws \Exception
	 * @param array $params
	 * @param bool $post
	 * @return mixed
	 */
	public function makeRequest( array $params, $post = false ) {
		self::$httpRequests++;

		$params = $params + array( 'format' => 'json' );

		if ( $post ) {
			$this->http->setMethod( Request::METHOD_POST );
			$this->http->setParameterGet( array() );
			$this->http->setParameterPost( $params );
		} else {
			$this->http->setMethod( Request::METHOD_GET );
			$this->http->setParameterGet( $params );
			$this->http->setParameterPost( array() );
		}

		\o::debug( $this->http->getRequest()->renderRequestLine() );
		if ( self::$nextRequest > ( $t = time() ) ) {
			$difference = self::$nextRequest - $t;
			\o::debug( "Throttling request for $difference seconds...", 1 );
			sleep( $difference );
		}

		$tries = 1;
		while ( true ) {
			try {
				$response = $this->http->send();
				break;
			} catch ( \Zend\Http\Client\Adapter\Exception\TimeoutException $e ) {
				if ( $tries == 3 ) {
					throw $e;
				}
				\o::warn( "Timeout, retrying... (try #$tries)", 2 );
				sleep( 5 );
				$tries++;
			}
		}

		self::$nextRequest = time() + 1;

		if ( $response->isServerError() ) {
			// FIXME: This is really bad
			throw new \Exception( 'Server error' );
		}

		$data = json_decode( $response->getBody(), true );

		if ( $data === false ) {
            echo $response->getBody();
            print_r($params);
			throw new \Exception( 'API returned invalid data' );
		}

		if ( isset( $data['error'] ) ) {
			$error = $data['error'];
			throw new ApiError( $error['code'], $error['info'] );
		}

		// TODO: Error checking

		return $data;
	}

	/**
	 * @return int
	 */
	public static function getHttpRequests() {
		return self::$httpRequests;
	}

}
