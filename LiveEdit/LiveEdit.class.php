<?php


class LiveEdit {

	/**
	 * Starts a new editing session
	 *
	 * @param $user User
	 * @param $title Title
	 * @param $section int|null
	 * @return int
	 */
	public static function startEditSession( $user, $title, $section ) {
		// Weird issue
		if ( $section === null ) {
			$section = 0;
		}

		// Start by checking to see if we have a session
		$dbr = wfGetDB( DB_SLAVE );
		$row = $dbr->selectRow( 'le_session', array(
				'les_id', 'les_state', 'les_last_seen'
			), array(
			'les_title' => $title->getPrefixedDBkey(),
			'les_user_text' => $user->getName(),
			'les_section' => $section,
			), __METHOD__
		);

		if ( self::isValidSession( $row ) ) {
			// Update that session
			self::updateEditSessionById( $row->les_id, null );

			return $row->les_id;
		}

		// Okay we need to delete that session first
		$dbw = wfGetDB( DB_MASTER );
		if ( $row !== false ) {
			/*$dbw->delete(
				'le_session', array( 'les_id' => $row->les_id ), __METHOD__
			);*/
		}

		$dbw->insert( 'le_session', array(
			'les_title' => $title->getPrefixedDBkey(),
			'les_section' => $section,
			'les_user' => $user->getId(),
			'les_user_text' => $user->getName(),
			'les_start' => wfTimestampNow(),
			'les_last_seen' => wfTimestampNow(),
			), __METHOD__, array( 'IGNORE' )
		);

		if ( !$dbw->affectedRows() ) {
			// Give up
			throw new MWException( 'We tried to create a new live edit session but failed' );
		}

		return $dbw->insertId();
	}

	/**
	 * Verifies that a session is still valid
	 *
	 * @param $row ResultWrapper|bool
	 * @return bool
	 */
	private static function isValidSession( $row ) {
		if ( $row === false ) {
			return false;
		}

		global $wgLiveEditPendingSessionTimeout, $wgLiveEditAbortedSessionTimeout,
			$wgLiveEditActiveSessionTimeout, $wgLiveEditPausedSessionTimeout;

		$timeoutChecks = array(
			'pending' => $wgLiveEditPendingSessionTimeout,
			'active' => $wgLiveEditActiveSessionTimeout,
			'paused' => $wgLiveEditPausedSessionTimeout,
			'aborted' => $wgLiveEditAbortedSessionTimeout,
			'complete' => 0,
		);

		$timeDiff =  wfTimestamp( TS_UNIX ) - $timeoutChecks[$row->les_state];
		if ( $timeDiff > wfTimestamp( TS_UNIX, $row->les_last_seen ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Updates session information
	 *
	 * @param $user User
	 * @param $title Title
	 * @param $state string
	 */
	public static function updateEditSession( $user, $title, $state = 'active' ) {
		$dbw = wfGetDB( DB_MASTER );

		$update = array(
			'les_last_seen' => wfTimestampNow(),
		);
		if ( $state !== null ) {
			$update['les_state'] = $state;
		}

		$dbw->update( 'le_session', $update, array(
				'les_title' => $title->getPrefixedDBKey(),
				'les_user_text' => $user->getName(),
				'LAST_INSERT_ID(les_id)',
			), __METHOD__
		);
	}

	/**
	 * Marks the edit session as complete
	 *
	 * @param $sessionId int
	 */
	public static function endEditSession( $sessionId ) {
		self::updateEditSessionById( $sessionId, 'complete' );
	}

	/**
	 * Returns an array of users editing the given title
	 *
	 * @param $title Title
	 * @return ResultWrapper
	 */
	public static function getUsersEditingArticle( Title $title ) {
		self::cleanupSessionTable();
		$dbr = wfGetDB( DB_SLAVE );
		$users = $dbr->select( 'le_session',
			array( 'les_user_text', 'les_state', 'les_start', 'les_last_seen', ),
			array( 'les_title' => $title->getPrefixedDBkey(), ),
			__METHOD__
		);
		return $users;
	}

	/**
	 * Returns the edit session specified by $editSessionId
	 *
	 * @param $editSessionId
	 * @return bool|ResultWrapper
	 */
	public static function getEditSession( $editSessionId ) {
		$dbr = wfGetDB( DB_SLAVE );
		return $dbr->selectRow( 'le_session',
			array( 'les_user_text', 'les_state' ),
			array( 'les_id' => intval( $editSessionId ) ),
			__METHOD__
		);
	}

	/**
	 * Updates session information
	 *
	 * @param $user User
	 * @param $title Title
	 * @param $state string
	 */
	public static function updateEditSessionById( $editSessionId, $state = 'active' ) {
		$dbw = wfGetDB( DB_MASTER );
		$update = array(
			'les_last_seen' => $dbw->timestamp(),
		);
		if ( $state !== null ) {
			$update['les_state'] = $state;
		}
		$dbw->update( 'le_session', $update, array(
				'les_id' => $editSessionId,
			), __METHOD__
		);
	}

	/**
	 * Cleans up the session table by removing old sessions
	 *
	 * @todo Retain aborted sessions?
	 *
	 * @return bool
	 */
	public static function cleanupSessionTable() {
		global $wgLiveEditCleanupChance, $wgLiveEditPendingSessionTimeout,
			$wgLiveEditActiveSessionTimeout, $wgLiveEditPausedSessionTimeout,
			$wgLiveEditAbortedSessionTimeout;

		if ( mt_rand() % $wgLiveEditCleanupChance ) {
			return false;
		}

		$dbw = wfGetDB( DB_MASTER );
		$startTimeout = $dbw->timestamp( wfTimestamp( TS_UNIX ) - $wgLiveEditPendingSessionTimeout );
		$activeTimeout = $dbw->timestamp( wfTimestamp( TS_UNIX ) - $wgLiveEditActiveSessionTimeout );
		$pausedTimeout = $dbw->timestamp( wfTimestamp( TS_UNIX ) - $wgLiveEditPausedSessionTimeout );
		$abortedTimeout = $dbw->timestamp( wfTimestamp( TS_UNIX ) - $wgLiveEditAbortedSessionTimeout );
		$dbw->query("
			DELETE FROM le_session
			WHERE les_state = 'complete'
				OR (les_state = 'pending' AND les_start < $startTimeout)
				OR (les_state = 'paused'  AND les_last_seen < $pausedTimeout)
				OR (les_state = 'active'  AND les_last_seen < $activeTimeout)
				OR (les_state = 'aborted' AND les_last_seen < $abortedTimeout)
		");
	}
}
