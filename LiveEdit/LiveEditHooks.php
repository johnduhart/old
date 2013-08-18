<?php

class LiveEditHooks {
	/**
	 * Session ID
	 *
	 * @var int|null
	 */
	private static $sessionId = null;

	/**
	 * Updates the database via update.php
	 *
	 * @param $updater DatabaseUpdater
	 * @return boolean
	 */
	public static function updateSchema( $updater = null ) {
		$dir = dirname( __FILE__ ) . '/sql/';
		$updater->addExtensionTable( 'le_session', $dir . 'le_session.sql' );
		return true;
	}

	/**
	 * Adds configuration variables to RL
	 *
	 * @param $vars
	 * @return bool
	 */
	public static function getConfigVars( &$vars ) {
		global $wgLiveEditSessionUpdateInterval, $wgLiveEditQueryInterval;

		$vars['wgLiveEditSessionUpdateInterval'] = $wgLiveEditSessionUpdateInterval * 1000;
		$vars['wgLiveEditQueryInterval'] = $wgLiveEditQueryInterval * 1000;

		return true;
	}

	/**
	 * Add a global module for session cleanup
	 *
	 * @param $modules
	 */
	public static function getStartupModules( &$modules ) {
		$modules[] = 'ext.liveEdit';

		return true;
	}

	/**
	 * @param $editPage EditPage
	 * @param $request WebRequest
	 * @return bool
	 */
	public static function importFormData( $editPage, $request ) {
		if ( $request->wasPosted() ) {
			$editPage->liveEditSessionId = $request->getIntOrNull( 'wpLiveEditSession' );
			$editPage->liveEditSessionActive = $request->getBool( 'wpLiveEditActive' );

			// Set the session id because we may never see it again
			if ( $editPage->liveEditSessionActive ) {
				self::$sessionId = $editPage->liveEditSessionId;
			}
		} else {
			$editPage->liveEditSessionId =
			$editPage->liveEditSessionActive = null;
		}

		return true;
	}

	/**
	 * @param $editPage EditPage
	 */
	public static function editFormInitial( $editPage ) {
		global $wgUser;

		// Check if it's a new page
		if ( !$editPage->mTitle->getArticleID() ) {
			return true;
		}

		// Did the javascript even kick in on the client?
		if ( !$editPage->firsttime && !$editPage->liveEditSessionActive ) {
			// Don't bother
			return true;
		}

		// Re start the session
		$editPage->liveEditSessionId = LiveEdit::startEditSession(
			$wgUser,
			$editPage->mTitle,
			$editPage->section
		);

		return true;
	}

	/**
	 * @param $editPage EditPage
	 * @param $output OutputPage
	 * @return bool
	 */
	public static function editFormFields( $editPage, $output ) {
		// Check to see if we have a session
		if ( $editPage->liveEditSessionId === null ) {
			return true;
		}

		// Add field to the form
		$output->addHTML( Html::hidden( 'wpLiveEditSession', $editPage->liveEditSessionId ) );
		$output->addHTML( Html::hidden( 'wpLiveEditActive', false, array( 'id' => 'liveEditActive' ) ) );

		// wgUserName isn't set if the user is anonymous
		$output->addJsConfigVars( 'liveEditUser', $output->getUser()->getName() );
		$output->addJsConfigVars( 'liveEditSessionId', $editPage->liveEditSessionId );
		$output->addModules( 'ext.liveEdit.edit' );

		return true;
	}

	/**
	 * @param $article WikiPage
	 * @param $user
	 * @param $text
	 * @param $isMinor
	 * @param $isWatch
	 * @param $section
	 * @param $flags
	 * @param $revision
	 * @param $status
	 * @param $baseRevId
	 * @param $redirect
	 * @return bool
	 */
	public static function articleSaveComplete(
		&$article, &$user, $text, $summary, $isMinor, $isWatch,
		$section, &$flags, $revision, &$status, $baseRevId
	) {
		// We don't track editors on new articles
		if ( $flags & EDIT_NEW ) {
			return true;
		}

		if ( self::$sessionId === null ) {
			return true;
		}

		LiveEdit::endEditSession( self::$sessionId );

		return true;
	}

	/**
	 * Adds the module to the page
	 *
	 * @param $article Article
	 * @param $outputDone bool
	 * @param $pcache string
	 */
	public static function articleViewHeader( &$article, &$outputDone, &$pcache ) {
		$article->getContext()->getOutput()->addModules( 'ext.liveEdit.read' );

		return true;
	}

	/**
	 * Adds the icon to the edit link
	 *
	 * @param $skin Skin
	 * @param $title title
	 * @param $section int
	 * @param $tooltip string
	 * @param $result string
	 * @param $lang string
	 */
	public static function doEditSectionLink( $skin, $title, $section, $tooltip, &$result, $lang ) {
		$iconDiv = Html::element( 'div', array(
			'class' => 'liveEdit-icons editsection',
			'data-sectionid' => $section,
		) );


		$result = $result . $iconDiv;

		$result = Html::rawElement( 'span', array( 'class' => 'liveEdit-wrap' ), $result );

		return true;
	}
}
