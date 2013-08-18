<?php

namespace Fruity\Modules\Api\Exceptions;

/**
 * Exception for logins
 */
class LoginException extends ApiException {
	/**
	 * Error codes that can be returned by the API
	 *
	 * @var array
	 */
	protected static $loginErrors = array(
		'NoName' => 'Username not specified',
		'Illegal' => 'Username with illegal characters specified',
		'NotExists' => 'Username specified does not exist',
		'EmptyPass' => 'Password not specified',
		'WrongPass' => 'Incorrect password specified',
		'WrongPluginPass' => 'Incorrect plugin password specified', // what is this i dont even
		'CreateBlocked' => 'IP address has been blocked',
		'Blocked' => 'User specified has been blocked',

		// Fruity errors:
		'_MaxRetry' => 'Maximum retry attempts reached',
	);

	/**
	 * Current error code for the exception
	 *
	 * @var string
	 */
	protected $errorCode;

	/**
	 * Creates an API login error
	 *
	 * @param string $errorCode Error code returned by API
	 */
	public function __construct( $errorCode ) {
		$message = 'A login error occurred: ';

		if ( isset( self::$loginErrors[$errorCode] ) ) {
			$message .= self::$loginErrors[$errorCode] . " ($errorCode)";
		} else {
			$message .= "Unknown error code: $errorCode";
		}

		$this->errorCode = $errorCode;
		parent::__construct( $message );
	}

	/**
	 * Returns the current API error code
	 *
	 * @see LoginException::$errorCode
	 * @return string
	 */
	public function getErrorCode() {
		return $this->errorCode;
	}
}
