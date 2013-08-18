<?php

require( '../Init.php' );

use Fruity\Core;

$wiki = Core::initWiki( 'exampleConfig.yml' );
//$pages = $wiki->getPagesInCategory( 'Category:Articles needing the year an event occurred', 0, true );

/*$pages = $wiki->getPagesWithPrefix( 'Wikipedia Signpost', 4, 100 );

foreach ( $pages->getTitles() as $page ) {
	echo $page . "\n";
}

exit;*/

/** @var \Fruity\Modules\Page\Page $page  */
$page = $wiki->getPage( 'User:RaptureBot/Test' );

$newText = $page->getText() . "\n\nYay this '''works'''";

$page->edit( $newText, 'Testing Fruity (PHP Bot framework)' );