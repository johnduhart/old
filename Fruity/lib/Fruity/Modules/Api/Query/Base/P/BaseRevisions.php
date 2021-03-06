<?php
/**
 * This file was generated by bartender and should not be modified
 */


/** @namespace */
namespace Fruity\Modules\Api\Query\Base\P;

abstract class BaseRevisions extends \Fruity\Modules\Api\Query\BaseQueryModule
{

    /**
     * Limit of results to return
     * 
     * @var null|integer
     * 
     */
    protected $limit = null;

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
     * @param int|null $limit
     * @return BaseBaseRevisions
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
     * Clears all the requested properties
     * 
     * @return BaseBaseRevisions
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
     * @return BaseBaseRevisions
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
     * @return BaseBaseRevisions
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
     * @return BaseBaseRevisions
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
     * @return BaseBaseRevisions
     * 
     */
    public function setAllProperties()
    {
        $this->properties = $this->validProperties;
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

