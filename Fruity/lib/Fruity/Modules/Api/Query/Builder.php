<?php

namespace Fruity\Modules\Api\Query;

use Fruity\Core,
	Fruity\Modules\Api\Api,
	Fruity\Modules\Api\Query\Result,
	Fruity\Modules\Api\Query\PageIterator,
	Fruity\Modules\Api\Query\QueryParameterCollector;

class Builder {

	/**
	 * The interface that all QueryModules must implement
	 *
	 * @var string
	 */
	static private $moduleInterface = 'Fruity\\Modules\\Api\\Query\\QueryModule';

	/**
	 * Array containing all module classes
	 *
	 * @var array
	 */
	static protected $moduleClasses = array(
		'meta' => array(),
		'list' => array(),
		'prop' => array(),
	);

	/**
	 * API that this builder belongs to
	 *
	 * @var \Fruity\Modules\Api\Modules\BaseApi\Api
	 */
	protected $api;

	/**
	 * Namespace of the query items
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Module chain
	 *
	 * @var array
	 */
	protected $modules;

	/**
	 * Titles to query for
	 *
	 * @var array
	 */
	protected $titles;

	/**
	 * Limit to apply to queries
	 *
	 * @var int|null
	 */
	protected $limit = false;

	/**
	 * Instead of post-processing the data from the API, return it in
	 * its raw form
	 *
	 * @var bool
	 */
	protected $rawMode = false;

	/**
	 * @var QueryParameterCollector
	 */
	protected $parameters;

	public static function addQueryModule( $type, $name, $class ) {
		if ( !isset( self::$moduleClasses[$type] ) ) {
			throw new \InvalidArgumentException( "Invalid type '$type' passed to " . __METHOD__ );
		}

		$name = strtolower( $name );
		self::$moduleClasses[$type][$name] = $class;
	}

	/**
	 * Constructor
	 *
	 * @param \Fruity\Modules\Api\Api $api
	 * @return \Fruity\Modules\Api\Query\Builder
	 */
	public function __construct( Api $api ) {
		$this->api = $api;
		$this->namespace = $api->getNamespace() . '\\Query';
		$this->clearModules();
		$this->clearTitles();
	}

	/**
	 * Clears the module chain
	 *
	 * @return Builder
	 */
	public function clearModules() {
		$this->modules = array();
		return $this;
	}

	/**
	 * Returns a meta module
	 *
	 * @throws \Exception
	 * @param $meta
	 * @return QueryModule
	 */
	public function createMeta( $meta ) {
		$class = self::$moduleClasses['meta'][strtolower($meta)];

		if ( class_exists( $class ) && Core::doesImplement( $class, self::$moduleInterface ) ) {
			return new $class( $this->api );
		} else {
			// TODO: Better exception
			throw new \Exception( 'Invalid meta module' );
		}
	}

	/**
	 * Returns an instance of a list module
	 *
	 * @throws \Exception
	 * @param $list
	 * @return
	 */
	public function createList( $list ) {
		$class = self::$moduleClasses['list'][strtolower($list)];

		if ( class_exists( $class ) && Core::doesImplement( $class, self::$moduleInterface ) ) {
			return new $class( $this->api );
		} else {
			// TODO: Better exception
			throw new \Exception( 'Invalid list module' );
		}
	}

	/**
	 * Returns an instance of a property module
	 *
	 * @throws \Exception
	 * @param $prop
	 * @return
	 */
	public function createProp( $prop ) {
		$class = self::$moduleClasses['prop'][strtolower($prop)];

		if ( class_exists( $class ) && Core::doesImplement( $class, self::$moduleInterface ) ) {
			return new $class( $this->api );
		} else {
			// TODO: Better exception
			throw new \Exception( 'Invalid prop module' );
		}
	}

	/**
	 * Adds a module to the chain
	 *
	 * @param QueryModule $module
	 * @return Builder
	 */
	public function add( QueryModule &$module ) {
		$this->modules[] =& $module;
		return $this;
	}

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

	/**
	 * @param boolean $rawMode
	 * @return Builder
	 */
	public function setRawMode( $rawMode ) {
		$this->rawMode = $rawMode;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isRawMode() {
		return $this->rawMode;
	}

	/**
	 * @param int|null $limit
	 * @return Builder
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
	 * @return \Fruity\Modules\Api\Query\Result
	 */
	public function execute() {
		$this->parameters = new QueryParameterCollector;
		$this->parameters
			->setTitles( $this->titles )
			->setLimit( $this->limit );

		$this->callModules( 'execute', array( $this->parameters ) );

		$data = $this->doRequest();

		// Check for query-continues
		if ( isset( $data['query-continue'] ) ) {
			$data = $this->queryContinueLoop( $data );
		}

		$this->callModules( 'processFinalData', array( &$data ) );

		if ( $this->isRawMode() ) {
			return $data;
		}
		$result = new Result();
		$result->setRawData( $data );

		$this->callModules( 'prepareResult', array( $data, $result ) );

		// Process pages
		if ( isset( $data['query']['pages'] ) ) {
			$this->processPages( $data['query']['pages'], $result );
		}

		if ( !$result->hasObjects() ) {
			// TODO: Remove, back-compat
			return $data;
		}

		return $result;
	}

	/**
	 * Handles query-continue cases
	 *
	 * @param array $data guess.
	 * @return array
	 */
	protected function queryContinueLoop( array $data ) {
		$originalData = $data;
		unset( $originalData['query-continue'] );

		do {
			$repeat = false;
			$this->parameters->resetContinue();

			$this->callModules( 'processQueryContinue', array( $data, $this->parameters ) );
			$repeat = $this->parameters->hasContinue();

			if ( $repeat ) {
				$data = $this->doRequest();
				$originalData['query'] = array_merge_recursive( $originalData['query'], $data['query'] );

				if ( !isset( $data['query-continue'] ) ) {
					$repeat = false;
				}
			}
		} while ( $repeat );

		return $originalData;
	}

	protected function processPages( array $pageData, Result $result ) {
		$result->setPageIterator(
			PageIterator
				::create( $pageData, $this->api->getWiki() )
				->setWakePages( false )
		);
	}

	protected function callModules( $function, array $params ) {
		foreach ( $this->modules as $module ) {
			call_user_func_array( array( $module, $function ), $params );
		}
	}

	protected function doRequest() {
		$params = $this->parameters->processParameters();
		$data = $this->api->makeRequest( $params );
		return $data;
	}
}
