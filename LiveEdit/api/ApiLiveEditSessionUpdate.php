<?php

/**
 * API module for updating the status of a live edit session
 */
class ApiLiveEditSessionUpdate extends ApiBase {

	/**
	 * Evaluates the parameters, performs the requested query, and sets up
	 * the result. Concrete implementations of ApiBase must override this
	 * method to provide whatever functionality their module offers.
	 * Implementations must not produce any output on their own and are not
	 * expected to handle any errors.
	 *
	 * The execute() method will be invoked directly by ApiMain immediately
	 * before the result of the module is output. Aside from the
	 * constructor, implementations should assume that no other methods
	 * will be called externally on the module before the result is
	 * processed.
	 *
	 * The result data should be stored in the ApiResult object available
	 * through getResult().
	 */
	public function execute() {
		$this->mustBePosted();

		$user = $this->getUser();
		$params = $this->extractRequestParams();

		$session = LiveEdit::getEditSession( $params['sessionid'] );

		if ( $session === false || $session->les_user_text != $user->getName() ) {
			$this->dieUsage(
				"There is no live edit session with the id ``{$params['sessionid']}''",
				'nosuchsessionid'
			);
		}

		LiveEdit::updateEditSessionById( $params['sessionid'], $params['state'] );

		$this->getResult()->addValue( null, $this->getModuleName(), array( 'updated' => '' ) );
	}

	/*public function mustBePosted() {
		return true;
	}*/

	/**
	 * Returns a string that identifies the version of the extending class.
	 * Typically includes the class name, the svn revision, timestamp, and
	 * last author. Usually done with SVN's Id keyword
	 *
	 * @return string
	 */
	public function getVersion() {
		// TODO: Better version numbers :)
		return __CLASS__ . 'lol';
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'code' => 'nosuchsessionid', 'info' => 'There is no live edit session with the id given' ),
		) );
	}

	protected function getAllowedParams() {
		return array(
			'sessionid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => 'integer'
			),
			'state' => array(
				ApiBase::PARAM_DFLT => 'active',
				ApiBase::PARAM_TYPE => array(
					'active',
					'paused',
					'aborted',
				),
			),
		);
	}
}
