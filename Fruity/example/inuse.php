<?php

define( 'TIMENOW', time() );

require( '../Init.php' );

use Fruity\Core,
	Fruity\Modules\Api\Query\PageIterator,
	Fruity\Modules\Api\Query\P\Revisions;

class InuseBot extends \Fruity\Bot {

	protected $logLines = array();

	public function run() {
		$wiki = $this->getWiki();
		$inusePages = $wiki
			->getPagesThatEmbed( 'Template:In use', 0 )
			->merge( $wiki->getPagesThatEmbed( 'Template:Inuse-section', 0 ) );
		$this->process( $inusePages, 18000, '5 hours', 'In use' );

		$inCreation = $wiki->getPagesThatEmbed( 'Template:In creation', 0 );
		$this->process( $inCreation, 18000, '5 hours', 'In creation' );

		$underConstruction = $wiki->getPagesThatEmbed( 'Template:Under construction', 0 );
		$this->process( $underConstruction, 432000, '5 days', 'Under construction' );

		$newPage = $wiki->getPagesThatEmbed( 'Template:New page', 0 );
		$this->process( $newPage, 432000, '5 days', 'New page' );

		$this->logSave();
	}

	protected function process( PageIterator $pages, $timediff, $timeHuman, $template ) {
		// Wake pages with full info
		$pages
			->setWakePages( true )
			->setWakeQueryCallback( function ( $queryBuilder, &$info ) {
				$revisions = $queryBuilder->createProp( 'revisions' );
				$revisions
					->setProperties( array(
						Revisions::PROP_IDS,
						Revisions::PROP_FLAGS,
						Revisions::PROP_TIMESTAMP,
						Revisions::PROP_USER,
						Revisions::PROP_COMMENT,
						Revisions::PROP_SIZE,
						Revisions::PROP_CONTENT,
						Revisions::PROP_TAGS,
					) )
					->setLimit( '' );
				$queryBuilder
					->add( $revisions );
			} );

		/** @var $page \Fruity\Modules\Page\Page */
		foreach ( $pages as $page ) {
			if ( $page->getLastRevision()->getTimestamp( FRUITY_TS_UNIX ) > TIMENOW - $timediff ) {
				\o::msg( "Skipping $page" );
				continue;
			}

			\o::msg( "Processing $page" );
			$templateObj = $page->getTemplate( $template );
			$templateObj->remove();

			//$page->save();
			$this->log( "Removing [[Template:$templateObj|$templateObj]] from [[$page]] because it has not ben edited in over $timeHuman" );
		}
	}

	protected function log( $msg ) {
		$this->logLines[] = $msg;
	}

	protected function logSave() {
		$logPage = $this->getWiki()->getPage( 'User:RaptureBot/InuseBot' );
		array_walk( $this->logLines, function( &$key ) {
				$key = "* $key\n";
			} );
		$logText = "Proof of Concept testing\n----\n" . implode( "\n", $this->logLines );
		$logPage->edit( $logText, "Updating log page for a Proof of Concept robot" );
		$this->logLines = array();
	}
}

Core::runBot( 'InuseBot', 'exampleConfig.yml' );
\o::info( \Fruity\Modules\Api\Api::getHttpRequests() . ' HTTP requests made' );