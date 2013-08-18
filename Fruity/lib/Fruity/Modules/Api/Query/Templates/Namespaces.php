<?php

class Namespaces {
	/**
	 * Array of namespaces to limit the query to
	 *
	 * @var array
	 */
	protected $namespaces = array();

	/**
	 * Sets the namespaces to limit to $namespaces
	 *
	 * @param array|null $namespaces
	 * @return BaseNamespaces
	 */
	public function setNamespaces( $namespaces ) {
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
	 * @return BaseNamespaces
	 */
	public function addNamespace( $namespace ) {
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
	 */
	public function getNamespaces() {
		return $this->namespaces;
	}

	/**
	 * Checks to see if there are namespaces in the list
	 *
	 * @return bool
	 */
	public function hasNamespaces() {
		return $this->namespaces !== null && count( $this->namespaces );
	}

	/**
	 * Returns API appropriate namespace list
	 *
	 * @return string
	 */
	public function getHttpNamespaces() {
		return $this->arrayToHttpValue( $this->namespaces );
	}
}
