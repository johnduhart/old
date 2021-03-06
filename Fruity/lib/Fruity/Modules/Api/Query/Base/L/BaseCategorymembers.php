<?php
/**
 * This file was generated by bartender and should not be modified
 */


/** @namespace */
namespace Fruity\Modules\Api\Query\Base\L;

abstract class BaseCategorymembers extends \Fruity\Modules\Api\Query\BaseQueryModule
{

    /**
     * Limit of results to return
     * 
     * @var null|integer
     * 
     */
    protected $limit = null;

    /**
     * Array of namespaces to limit the query to
     * 
     * @var array
     * 
     */
    protected $namespaces = array();

    /**
     * Array containing all the properties to fetch for the request
     * 
     * @var array
     * 
     */
    public $properties = array();

    /**
     * Array of properties that are valid
     * 
     * @var array
     * 
     */
    protected $validProperties = array();

    /**
     * Title of the category to search
     * 
     * @var null
     * 
     */
    protected $title = null;

    /**
     * @param int|null $limit
     * @return BaseBaseCategorymembers
     * 
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        		return $this;
    }

    /**
     * @return int|null
     * 
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Checks whether $count is over or equal to limit
     * 
     * @param int $count
     * @param bool $greaterThan Return false only if $count is greater
     * than the limit
     * @return bool
     * 
     */
    public function overLimit($count, $greaterThan = 'false')
    {
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
     * 
     */
    public function getHttpLimit()
    {
        return ( $this->limit === null ) ? 'max' : $this->limit;
    }

    /**
     * Sets the namespaces to limit to $namespaces
     * 
     * @param array|null $namespaces
     * @return BaseBaseCategorymembers
     * 
     */
    public function setNamespaces($namespaces)
    {
        if ( $namespaces === null ) {
        			$this->namespaces = null;
        		} else {
        			$this->namespaces = (array) $namespaces;
        		}
        		return $this;
    }

    /**
     * Adds a namespace to the list
     * 
     * @param $namespace
     * @return BaseBaseCategorymembers
     * 
     */
    public function addNamespace($namespace)
    {
        if ( !is_array( $this->namespaces ) ) {
        			$this->namespaces = array();
        		}
        		$this->namespaces[] = $namespace;
        		return $this;
    }

    /**
     * Returns the namespace list
     * 
     * @return array
     * 
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Checks to see if there are namespaces in the list
     * 
     * @return bool
     * 
     */
    public function hasNamespaces()
    {
        return $this->namespaces !== null && count( $this->namespaces );
    }

    /**
     * Returns API appropriate namespace list
     * 
     * @return string
     * 
     */
    public function getHttpNamespaces()
    {
        return $this->arrayToHttpValue( $this->namespaces );
    }

    /**
     * Clears all the requested properties
     * 
     * @return BaseBaseCategorymembers
     * 
     */
    public function clearProperties()
    {
        $this->properties = array();
        		return $this;
    }

    /**
     * Sets requested properties
     * 
     * @param $properties
     * @return BaseBaseCategorymembers
     * 
     */
    public function setProperties($properties)
    {
        $this->clearProperties();
        		$this->addProperties( $properties );
        		return $this;
    }

    /**
     * Adds an array of properties to the list
     * 
     * @param $properties
     * @return BaseBaseCategorymembers
     * 
     */
    public function addProperties($properties)
    {
        $properties = (array) $properties;

        		foreach ( $properties as $property ) {
        			$this->addProperty( $property );
        		}
        		return $this;
    }

    /**
     * Add a property
     * 
     * @param $property
     * @return BaseBaseCategorymembers
     * 
     */
    public function addProperty($property)
    {
        if ( in_array( $property, $this->validProperties ) ) {
        			$this->properties[] = $property;
        		} else {
        			\o::warn( "Invalid property ($property) passed to " . __METHOD__, 1 );
        		}
        		return $this;
    }

    /**
     * Sets all the properties
     * 
     * @return BaseBaseCategorymembers
     * 
     */
    public function setAllProperties()
    {
        $this->properties = $this->validProperties;
        		return $this;
    }

    /**
     * Set the title of the category to search though
     * 
     * @param string $title
     * @return Categorymembers
     * 
     */
    public function setTitle($title)
    {
        $this->title = $title;
        		return $this;
    }

    /**
     * Converts an array of values into a HTTP parameter separated by pipes
     * 
     * @param array $values
     * @return string
     * 
     */
    protected function arrayToHttpValue($values)
    {
        if ( $values === null ) {
        			return null;
        		}
        		return implode( '|', array_map( array( $this, 'cleanUrlbit' ), (array) $values ) );
    }

    private function cleanUrlBit($str)
    {
        $str = rawurlencode( $str );
        		return str_replace( array( '%3A' ), array( ':' ), $str );
    }

    /**
     * Called if a query-continue is present in the returned data so the module
     * can process it and set the appropriate parameters
     * 
     * @param array $data Data returned from the request
     * @param \Fruity\Modules\Api\Modules\HttpApi\Query\ParameterCollector $parameters
     * Parameter Collector
     * @return void
     * 
     */
    public function processQueryContinue($data, $parameters)
    {
        // Do nothing unless overridden
    }

    /**
     * Processes the final data returned by the API
     * 
     * @param array $data
     * @return void
     * 
     */
    public function processFinalData(&$data)
    {
        // Do nothing unless overridden
    }

    /**
     * Prepares data in a form Fruity can use regardless of Api
     * 
     * @param mixed $data
     * @param Result $result
     * @return void
     * 
     */
    public function prepareResult($data, $result)
    {
        // Do nothing unless overridden
    }

    /**
     * Turns $data['query'][...] into $data[...] and returns false if the query
     * key doesn't exist
     * 
     * @param $data
     * @return bool
     * 
     */
    protected function convertToQuery(&$data)
    {
        if ( !isset( $data['query'] ) ) {
        			return false;
        		}

        		$data = $data['query'];
        		return true;
    }


}

