<?php

class Properties {
	/**
	 * Array containing all the properties to fetch for the request
	 *
	 * @var array
	 */
	public $properties = array();

	/**
	 * Array of properties that are valid
	 *
	 * @var array
	 */
	protected $validProperties = array();

	/**
	 * Clears all the requested properties
	 *
	 * @return BaseProperties
	 */
	public function clearProperties() {
		$this->properties = array();
		return $this;
	}

	/**
	 * Sets requested properties
	 *
	 * @param $properties
	 * @return BaseProperties
	 */
	public function setProperties( $properties ) {
		$this->clearProperties();
		$this->addProperties( $properties );
		return $this;
	}

	/**
	 * Adds an array of properties to the list
	 *
	 * @param $properties
	 * @return BaseProperties
	 */
	public function addProperties( $properties ) {
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
	 * @return BaseProperties
	 */
	public function addProperty( $property ) {
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
	 * @return BaseProperties
	 */
	public function setAllProperties() {
		$this->properties = $this->validProperties;
		return $this;
	}
}
