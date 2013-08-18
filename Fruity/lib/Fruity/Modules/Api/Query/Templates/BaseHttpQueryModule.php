<?php

class BaseHttpQueryModule {
	/**
	 * Converts an array of values into a HTTP parameter separated by pipes
	 *
	 * @param array $values
	 * @return string
	 */
	protected function arrayToHttpValue( $values ) {
		if ( $values === null ) {
			return null;
		}
		return implode( '|', array_map( array( $this, 'cleanUrlbit' ), (array) $values ) );
	}

	private function cleanUrlBit( $str ) {
		$str = rawurlencode( $str );
		return str_replace( array( '%3A' ), array( ':' ), $str );
	}

	/**
	 * Called if a query-continue is present in the returned data so the module
	 * can process it and set the appropriate parameters
	 *
	 * @param array $data Data returned from the request
	 * @param \Fruity\Modules\Api\Modules\HttpApi\Query\ParameterCollector $parameters Parameter Collector
	 * @return void
	 */
	public function processQueryContinue( $data, $parameters ) {
		// Do nothing unless overridden
	}

	/**
	 * Processes the final data returned by the API
	 *
	 * @param array $data
	 * @return void
	 */
	public function processFinalData( &$data ) {
		// Do nothing unless overridden
	}

	/**
	 * Prepares data in a form Fruity can use regardless of Api
	 *
	 * @param mixed $data
	 * @param Result $result
	 * @return void
	 */
	public function prepareResult( $data, $result ) {
		// Do nothing unless overridden
	}

	/**
	 * Turns $data['query'][...] into $data[...] and returns false if the query
	 * key doesn't exist
	 *
	 * @param $data
	 * @return bool
	 */
	protected function convertToQuery( &$data ) {
		if ( !isset( $data['query'] ) ) {
			return false;
		}

		$data = $data['query'];
		return true;
	}
}
