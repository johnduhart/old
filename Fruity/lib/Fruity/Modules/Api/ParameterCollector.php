<?php

namespace Fruity\Modules\Api;

use Fruity\Modules\Api\Exceptions\MissingParameter;

class ParameterCollector {
	/**
	 * Temporary spot to put URL parameters
	 *
	 * @var array
	 */
	public $urlParameters = array();

	/**
	 * Sets the action
	 *
	 * @param $action
	 * @return ParameterCollector
	 */
	public function setAction( $action ) {
		$this->urlParameters['action'] = $action;
		return $this;
	}

	/**
	 * Adds a parameter
	 *
	 * @param $param
	 * @param $value
	 * @return ParameterCollector
	 */
	public function setParameter( $param, $value ) {
		$this->urlParameters[$param] = $value;
		return $this;
	}

	/**
	 * Checks to see if a parameter is empty
	 *
	 * @param $value
	 * @return bool
	 */
	public function parameterEmpty( $value ) {
		if ( $value === null ) {
			return true;
		} elseif ( is_array( $value ) && !count( $value ) ) {
			return true;
		} elseif ( is_string( $value ) && trim( $value ) == '' ) {
			return true;
		} elseif ( is_bool( $value ) && $value === false ) {
			return true;
		}

		return false;
	}

	/**
	 * Sets a parameter only if it's not empty
	 *
	 * @param $param
	 * @param $value
	 * @return ParameterCollector
	 */
	public function setParameterIf( $param, $value ) {
		if ( $this->parameterEmpty( $value ) ) {
			return $this;
		}

		return $this->setParameter( $param, $value );
	}

	/**
	 * Requires a parameter to not be empty otherwise throws an exception
	 *
	 * @throws \Fruity\Modules\Api\Query\Exceptions\MissingParameter
	 * @param $param
	 * @param $value
	 * @return ParameterCollector
	 */
	public function setParameterRequired( $param, $value ) {
		if ( $this->parameterEmpty( $value ) ) {
			throw new MissingParameter( __CLASS__, $param );
		}

		return $this->setParameter( $param, $value );
	}

	/**
	 * Converts array parameter values to pipe-based and sets the action to query
	 *
	 * @return array
	 */
	public function processParameters() {
		$params = array();
		foreach ( $this->urlParameters AS $name => $value ) {
			if ( is_array( $value ) ) {
				$params[$name] = implode( '|', $value );
			} else {
				$params[$name] = $value;
			}
		}

		return $params;
	}
}
