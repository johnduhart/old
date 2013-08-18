<?php
/**
 * LiveEdit - Realtime notifications of editing
 *
 * @file
 * @author John Du Hart
 */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'LiveEdit',
	'url' => 'http://www.mediawiki.org/wiki/Extension:LiveEdit',
	'author' => 'John Du Hart',
	'descriptionmsg' => 'liveedit-desc',
);

/*
 * Configuration
 */

/**
 * Chance that the query to clean up the session table will be ran
 *
 * @var $wgLiveEditCleanupChance int
 */
$wgLiveEditCleanupChance = 2;

/**
 * How many seconds to wait for the client to register via the API
 *
 * @var $wgLiveEditPendingSessionTimeout int
 */
$wgLiveEditPendingSessionTimeout = 60;

/**
 * How many seconds to wait for the client to call home again before deleting
 * their session.
 *
 * @var $wgLiveEditActiveSessionTimeout int
 */
$wgLiveEditActiveSessionTimeout = 180;

/**
 * How many seconds to wait for the client to call home again for a session
 * that left the edit page before considering it dead
 *
 * @var $wgLiveEditPausedSessionTimeout int
 */
$wgLiveEditPausedSessionTimeout = 100;

/**
 * How many seconds to wait before discarding aborted sessions
 *
 * The reason we wait is because if a user opens another edit form for the same
 * article and then leaves it, the original one would be left with no session,
 * so we give it a chance to call home again and reactivate it
 *
 * @var $wgLiveEditAbortedSessionTimeout int
 */
$wgLiveEditAbortedSessionTimeout = 30;

/**
 * How many seconds between session updates
 *
 * @var $wgLiveEditSessionUpdateInterval int
 */
$wgLiveEditSessionUpdateInterval = 5;

/**
 * How many seconds between editing queries
 *
 * @var $wgLiveEditQueryInterval
 */
$wgLiveEditQueryInterval = 5;

/*
 * Init!
 */
$dir = dirname( __FILE__ );

// Messages
$wgExtensionMessagesFiles['LiveEdit'] = "$dir/LiveEdit.i18n.php";

// Autoload
$wgAutoloadLocalClasses['ApiQueryLiveEditSessions'] = "$dir/api/ApiQueryLiveEditSessions.php";
$wgAutoloadLocalClasses['ApiLiveEditSessionUpdate'] = "$dir/api/ApiLiveEditSessionUpdate.php";
$wgAutoloadLocalClasses['LiveEdit'] = "$dir/LiveEdit.class.php";
$wgAutoloadLocalClasses['LiveEditHooks'] = "$dir/LiveEditHooks.php";

// Hooks
$wgHooks['LoadExtensionSchemaUpdates'][] = 'LiveEditHooks::updateSchema';
$wgHooks['ResourceLoaderGetConfigVars'][] = 'LiveEditHooks::getConfigVars';
$wgHooks['ResourceLoaderGetStartupModules'][] = 'LiveEditHooks::getStartupModules';
$wgHooks['EditPage::importFormData'][] = 'LiveEditHooks::importFormData';
$wgHooks['EditPage::showEditForm:initial'][] = 'LiveEditHooks::editFormInitial';
$wgHooks['EditPage::showEditForm:fields'][] = 'LiveEditHooks::editFormFields';
$wgHooks['ArticleSaveComplete'][] = 'LiveEditHooks::articleSaveComplete';
$wgHooks['ArticleViewHeader'][] = 'LiveEditHooks::articleViewHeader';
$wgHooks['DoEditSectionLink'][] = 'LiveEditHooks::doEditSectionLink';

// API
$wgAPIPropModules['liveeditsessions'] = 'ApiQueryLiveEditSessions';
$wgAPIModules['liveeditsessionupdate'] = 'ApiLiveEditSessionUpdate';

// Resource loader
$commonModuleInfo = array(
	'localBasePath' => dirname( __FILE__ ) . '/resources',
	'remoteExtPath' => 'LiveEdit/resources',
);

$wgResourceModules['ext.liveEdit'] = array(
	'scripts' => 'ext.liveEdit.js',
	'messages' => array(
	),
	'dependencies' => array(
	),
) + $commonModuleInfo;

$wgResourceModules['ext.liveEdit.edit'] = array(
	'scripts' => 'ext.liveEdit.edit.js',
	'styles' => 'ext.liveEdit.edit.css',
	'messages' => array(
		'liveedit-editpage-text',
	),
	'dependencies' => array(
		'ext.liveEdit',
	),
) + $commonModuleInfo;

$wgResourceModules['ext.liveEdit.read'] = array(
	'scripts' => 'ext.liveEdit.read.js',
	'styles' => 'ext.liveEdit.read.css',
	'messages' => array(
		'liveedit-editing',
		'liveedit-editing-section',
	),
	'dependencies' => array(
		'ext.liveEdit',
		'jquery.tipsy',
	),
) + $commonModuleInfo;