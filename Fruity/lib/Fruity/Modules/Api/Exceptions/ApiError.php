<?php

namespace Fruity\Modules\Api\Exceptions;

/**
 * Represents an error returned by the API
 */
class ApiError extends ApiException {
	/**
	 * Error code returned by the API
	 *
	 * @var string
	 */
	protected $errorCode;

	/**
	 * Error info string returned by the API
	 *
	 * @var string
	 */
	protected $errorInfo;

	/**
	 * Constructor
	 *
	 * @param string $errorCode Code
	 * @param string $errorInfo Info string
	 */
	public function __construct( $errorCode, $errorInfo ) {
		$this->errorCode = $errorCode;
		$this->errorInfo = $errorInfo;

		$message = "The API returned an error: '$errorInfo' (Code: $errorCode) ";
		parent::__construct( $message );
	}

	/**
	 * @return string
	 */
	public function getErrorCode() {
		return $this->errorCode;
	}

	/**
	 * @return string
	 */
	public function getErrorInfo() {
		return $this->errorInfo;
	}
}
