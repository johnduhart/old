<?php

class ApiQueryLiveEditSessions extends ApiQueryBase {

	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'les' );
	}

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
		LiveEdit::cleanupSessionTable();

		$this->addTables( array( 'le_session' ) );
		$this->addFields( array( 'les_title', 'les_user_text', 'les_state', 'les_start', 'les_section' ) );

		$titles = $this->getPageSet()->getGoodTitles();
		$dbTitles = array();
		/** @var $title Title */
		foreach ( $titles as $id => $title ) {
			$dbTitles[$id] = $title->getPrefixedDBkey();
		}

		$this->addWhere( array(
			'les_title' => $dbTitles,
			'les_state' => array( 'active', 'paused' ),
		) );
		$this->addOption( 'ORDER BY', 'les_start' );

		$res = $this->select( __METHOD__ );

		$pageIds = array_flip( $dbTitles );
		foreach ( $res as $row ) {
			$vals = array(
				'user' => $row->les_user_text,
				'state' => $row->les_state,
				'start' => wfTimestamp( TS_ISO_8601, $row->les_start ),
				'section' => $row->les_section,
			);

			$this->addPageSubItem( $pageIds[$row->les_title], $vals );
		}
	}

	/**
	 * Returns a string that identifies the version of the extending class.
	 * Typically includes the class name, the svn revision, timestamp, and
	 * last author. Usually done with SVN's Id keyword
	 * @return string
	 */
	public function getVersion() {
		return __CLASS__ . ': lol';
	}
}
