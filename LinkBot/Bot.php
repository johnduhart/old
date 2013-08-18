<?php

use Fruity\Core;
use Fruity\Bot;
use Fruity\Namespaces;

class LinkBot extends Bot {

	const projectRegex = '#(?:https?:)?//(?<lang>[a-z\-]+)\.(?<project>wikipedia|wiktionary|wikiquote|wikibooks|wikisource|wikinews|wikiversity).org/wiki/(?<article>.+)#i';

	const otherWikiRegx = '#(?:https?:)?//(?<host>(?:(?:commons|meta|species|incubator|strategy|outreach|quality|otrs-wiki|usability|wikimania(?:\d{4}|team)|br|fi|nl|nyc|no|pl|rs|ru|se|uk).wikimedia|wikimediafoundation|wikisource|www.mediawiki).org|translatewiki.net)/wiki/(?<article>.*)#i';

	const bugzillaRegex = '/(?:https?:)?\/\/bugzilla.wikimedia.org\/(show_bug.cgi\?id=)?(?<b>\d+)(?<c>#c\d+)?/i';

	const blogRegex = '#(?:https?:)?//(tech)?blog.wikimedia.org/(?<link>[a-z0-9/\-]+)#i';

	const dumpsRegex = '#(?:https?:)?//(?:download|dumps).wikimedia.org/(?<link>.+)#i';

	const etherpadRegex = '#(?:https?:)?//(?:etherpad|eiximenis).wikimedia.org/(?<pad>[a-z0-9\-]+)#i';

	const mailRegex = '#(?:https?:)?//(?:lists|mail).wiki[mp]edia.org/mailman/listinfo/(?<list>[a-z0-9\-]+)#i';

	const mailArchiveRegex = '#(?:https?:)?//(?:lists|mail).wikimedia.org/pipermail/(?<link>.+)#i';

	const statsRegex = '#(?:https?:)?//stats.wikimedia.org/(?<link>.+)#i';

	const wikitechRegex = '#(?:https?:)?//wikitech.wikimedia.org/view/(?<article>.*)#i';

	protected static $projectPrefixes = array(
		'wikipedia' => 'w',
		'wiktionary' => 'wikt',
		'wikibooks' => 'b',
		'wikinews' => 'n',
		'wikiquote' => 'q',
		'wikisource' => 's',
		'wikiversity' => 'v',
		'testwiki' => '',
	);

	protected static $otherPrefixes = array(
		'commons.wikimedia.org' => 'commons',
		'meta.wikimedia.org' => 'm',
		'species.wikimedia.org' => 's',
		'incubator.wikimedia.org' => 'incubator',
		'strategy.wikimedia.org' => 'strategy',
		'outreach.wikimedia.org' => 'outreach',
		'quality.wikimedia.org' => 'quality',
		'otrs-wiki.wikimedia.org' => 'otrswiki',
		'usability.wikimedia.org' => 'usability',

		'br.wikimedia.org' => 'wmbr',
		'fi.wikimedia.org' => 'wmfi',
		'nl.wikimedia.org' => 'wmnl',
		'nyc.wikimedia.org' => 'wmnyc',
		'no.wikimedia.org' => 'wmno',
		'pl.wikimedia.org' => 'wmpl',
		'rs.wikimedia.org' => 'wmrs',
		'ru.wikimedia.org' => 'wmru',
		'se.wikimedia.org' => 'wmse',
		'uk.wikimedia.org' => 'wmuk',

		'wikimania.wikimedia.org' => 'wmania',
		'wikimaniateam.wikimedia.org' => 'wmteam',
		'wikimania2006.wikimedia.org' => 'wm2006',
		'wikimania2007.wikimedia.org' => 'wm2007',
		'wikimania2008.wikimedia.org' => 'wm2008',
		'wikimania2009.wikimedia.org' => 'wm2009',
		'wikimania2010.wikimedia.org' => 'wm2010',
		'wikimania2011.wikimedia.org' => 'wm2011',
		'wikimania2012.wikimedia.org' => 'wm2012',

		'wikimediafoundation.org' => 'wmf',
		'www.mediawiki.org' => 'mw',
		'wikisource.org' => 'oldwikisource',
		'translatewiki.net' => 'translatewiki',
	);

	protected $filenamespaces = array();

	protected $url = '';

	protected $page = '';

	/**
	 * Function ran by Core to start the robot
	 */
	public function run() {
		$this->setupNamespaces();

		if ( $this->url != '' ) {
			$pages = $this->getWiki()->getPagesWithExternalUrl( $this->url );
		} elseif ( $this->page != '' ) {
			$pages = $this->getWiki()->getPages( array( $this->page ) );
		} else {
			throw new Exception( 'No page source given' );
		}

		$pages
			->unique()
			->setWakePages( true )
			->setWakeQueryCallback( function( $queryBuilder, &$info ) {
				$queryBuilder
					->add( $queryBuilder->createProp( 'extlinks' ) )
					->add( $queryBuilder->createProp( 'revisions' )->addProperties( array( 'ids', 'content' ) ) );
			} );
		$this->processPages( $pages );
	}

	public function processPages( $pages ) {
		/** @var $page \Fruity\Modules\Page\Page */
		foreach ( $pages as $page ) {
			// Stay away from stuff in the help namespace, mostly examples
			if ( $page->inNamespace( Namespaces::Help ) || $page->inNamespace( Namespaces::MediaWiki ) ) {
				continue;
			}

			// Can't touch that
			if ( $page->isUserJsOrCssPage() ) {
				continue;
			}

			// Don't convert your own userpage, silly
			if ( $page->getUnprefixedTitle() == 'External Link to Interwiki (Bot)' ) {
				continue;
			}

			$links = $page->getExternalLinks();
			$text = $oldText = $page->getText();
			\o::msg( "Processing {$page->getTitle()}" );

			$linksProcessed = 0;
			foreach ( $links as $link ) {
				$link = $link['*'];
				$pass = 0;

				do {
					$r = $this->linkPass( $link, $text, ++$pass );

					if ( $r ) {
						$linksProcessed++;
					}
				} while( $r );
			}

			if ( $oldText == $text ) {
				continue;
			}

			try {
				$page->edit( $text,
					"Update to use wikilinks, $linksProcessed links replaced ([[Project:Bots|BOT Edit]])", true
				);
			} catch ( \Fruity\Modules\Api\Exceptions\ApiError $e ) {
				\o::error( $e->getErrorInfo() );
			} catch ( \Zend\Http\Client\Adapter\Exception\TimeoutException $e ) {
				\o::error( "Timeout exception, continuing..." );
			}
		}
	}

	protected function linkPass( $link, &$text, $pass ) {
		// Do a quick check to see if we can find the link
		$regexLink = $this->buildRegexLink( $link );
		if ( !preg_match( "#{$regexLink}#", $text ) ) {
			if ( $pass == 1 ) {
				//\o::warn( "Couldn't find the link on first pass: $link", 1 );
			}
			return false;
		}

		if ( preg_match( self::projectRegex, $link, $matches ) ) {
			\o::msg( 'Processing project link: ' . $link, 1 );
			return $this->processProjectLink( $text, $link, $matches );
		} elseif ( preg_match( self::otherWikiRegx, $link, $matches ) ) {
			\o::msg( 'Processing other project link: ' . $link, 1 );
			return $this->processOtherProjectLink( $text, $link, $matches );
		} elseif ( preg_match( self::bugzillaRegex, $link, $matches ) ) {
			\o::msg( 'Processing bugzilla link: ' . $link, 1 );
			return $this->processBugzillaLink( $text, $link, $matches );
		} elseif ( preg_match( self::blogRegex, $link, $matches ) ) {
			\o::msg( "Processing blog link: $link", 1 );
			return $this->processBlogLink( $text, $link, $matches );
		} elseif ( preg_match( self::dumpsRegex, $link, $matches ) ) {
			\o::msg( "Processing dumps link: $link", 1 );
			return $this->processDumpsLink( $text, $link, $matches );
		} elseif ( preg_match( self::etherpadRegex, $link, $matches ) ) {
			\o::msg( "Processing etherpad link: $link", 1 );
			return $this->processEtherpadLink( $text, $link, $matches );
		} elseif ( preg_match( self::mailRegex, $link, $matches ) ) {
			\o::msg( "Processing mail link: $link", 1 );
			return $this->processMailLink( $text, $link, $matches );
		} elseif ( preg_match( self::mailArchiveRegex, $link, $matches ) ) {
			\o::msg( "Processing mail archive link: $link", 1 );
			return $this->processMailArchiveLink( $text, $link, $matches );
		} elseif ( preg_match( self::statsRegex, $link, $matches ) ) {
			\o::msg( "Processing stats link: $link", 1 );
			return $this->processStatsLink( $text, $link, $matches );
		} elseif ( preg_match( self::wikitechRegex, $link, $matches ) ) {
			\o::msg( "Processing wikitech link: $link", 1 );
			return $this->processWikitechLink( $text, $link, $matches );
		}

		return false;
	}

	protected function processProjectLink( &$text, $link, $matches ) {
		$lang = $matches['lang'];
		$project = $matches['project'];
		$article = $matches['article'];
		$linkText = null;

		if ( !$this->checkArticle( $article ) ) {
			return false;
		}

		if ( $lang == 'www' ) {
			$lang = 'en';
		}

		if ( $lang == 'test' ) {
			$prefix = 'testwiki:';
		} elseif( $lang == 'test2' ) {
			return false;
		} elseif ( $lang == 'ten' ) {
			$prefix = 'tenwiki:';
		} elseif ( $lang == 'nostalgia' ) {
			$prefix = 'nost:';
		} elseif ( $project == 'wikiversity' && $lang == 'beta' ) {
			$prefix = 'betawikiversity:';
		} else {
			$prefix = self::$projectPrefixes[$project] . ":$lang:";

			// Check to see if this is the same project & language
			if ( $this->getWiki()->getHost() == "$lang.$project.org" ) {
				// No prefix, just a wikilink please
				$prefix = '';
			} elseif ( stripos( $this->getWiki()->getHost(), "$project.org") !== false  ) {
				// Same project, different langauge
				$prefix = ":$lang:";
			} elseif ( stripos( $this->getWiki()->getHost(), "$lang." ) === 0 ) {
				// Different project, same language
				$prefix = "$project:";
			}
		}

		// So we don't create image links
		if ( $prefix == '' && $this->inFileNamespace( $article ) ) {
			$prefix = ':';
		}

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$this->cleanArticle( $article );
		$newLink = "[[{$prefix}{$article}{$linkText}]]";

		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function cleanArticle( &$article ) {
		$article = str_replace( array(
				'%26',
				'%27',
				'%28',
				'%29',
				'%2B',
				'%2C',
				'_',
			), array(
				'&',
				'\'',
				'(',
				')',
				'+',
				',',
				' ',
			), $article
		);
	}

	protected function buildRegexLink( $link ) {
		$regexLink = preg_quote( $link, '#' );
		$regexLink = str_replace( array(
				'&',
				'\'',
				'\(',
				'\)',
				'\+',
				','
			), array(
				'(&|%26)',
				'(\'|%27)',
				'(\(|%28)',
				'(\)|%29)',
				'(\+|%2B)',
				'(,|%2C)',
			), $regexLink
		);
		return $regexLink;
	}

	/**
	 * Checks that the article title is valid
	 *
	 * @param $article
	 * @return bool
	 */
	protected function checkArticle( $article ) {
		// TODO: Check for invalid title chars
		if ( strstr( $article, '?' ) !== false ) {
			\o::msg( "Skipping, '?' found in article path", 2 );
			return false;
		} elseif ( substr( $article, 0, 1 ) == '$' ) {
			\o::msg( "Skipping, '$' found in the first character" );
			return false;
		}

		return true;
	}

	protected function processOtherProjectLink( &$text, $link, $matches ) {
		$host = $matches['host'];
		$article = $matches['article'];
		$linkText = null;

		if ( !$this->checkArticle( $article ) ) {
			return false;
		}

		$prefix = self::$otherPrefixes[$host] . ':';

		if ( $this->getWiki()->getHost() == $host ) {
			$prefix = '';
		}

		// So we don't create image links
		if ( $prefix == '' && $this->inFileNamespace( $article ) ) {
			$prefix = ':';
		}

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$this->cleanArticle( $article );
		$newLink = "[[{$prefix}{$article}{$linkText}]]";

		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function processBugzillaLink( &$text, $link, $matches ) {
		$id = $matches['b'];
		$comment = ( isset( $matches['c'] ) ) ? $matches['c'] : '';

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$newLink = "[[bugzilla:{$id}{$comment}{$linkText}]]";
		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function processBlogLink( &$text, $link, $matches ) {
		$blogLink = $matches['link'];

		// Clean up the old URL format
		if ( substr( $blogLink, 0, 5 ) == 'blog/' ) {
			$blogLink = substr( $blogLink, 5 );
		}

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$newLink = "[[WMFBlog:{$blogLink}{$linkText}]]";
		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function processDumpsLink( &$text, $link, $matches ) {
		$dumpLink = $matches['link'];

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$newLink = "[[download:{$dumpLink}{$linkText}]]";
		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function processEtherpadLink( &$text, $link, $matches ) {
		$pad = $matches['pad'];

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$newLink = "[[etherpad:{$pad}{$linkText}]]";
		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function processMailLink( &$text, $link, $matches ) {
		$list = $matches['list'];

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$newLink = "[[mail:{$list}{$linkText}]]";
		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function processMailArchiveLink( &$text, $link, $matches ) {
		$mailLink = $matches['link'];

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$newLink = "[[mailarchive:{$mailLink}{$linkText}]]";
		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function processStatsLink( &$text, $link, $matches ) {
		$statsLink = $matches['link'];

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$newLink = "[[stats:{$statsLink}{$linkText}]]";
		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function processWikitechLink( &$text, $link, $matches ) {
		$article = $matches['article'];

		if ( !$this->checkArticle( $article ) ) {
			return false;
		}

		if ( !( $linkMatch = $this->doLinkRegex( $text, $link ) ) ) {
			return false;
		}

		if ( isset( $linkMatch['text'] ) ) {
			$linkText = "|{$linkMatch['text']}";
		} else {
			$linkText = '';
		}

		$this->cleanArticle( $article );
		$newLink = "[[wikitech:{$article}{$linkText}]]";

		// Replace the old link
		$text = str_replace( $linkMatch[0], $newLink, $text );
		return true;
	}

	protected function doLinkRegex( $text, $link ) {
		$regexLink = $this->buildRegexLink( $link );

		if ( !preg_match( "#\[{$regexLink}(?: (?<text>.*?))?\]#", $text, $linkMatch ) ) {
			if ( !preg_match( "#(?<!\[){$regexLink}#", $text, $linkMatch ) ) {
				\o::warn( "Couldn't find the link...", 2 );
				return false;
			}
		}

		return $linkMatch;
	}

	/**
	 * Sets up the list of file namespaces
	 */
	protected function setupNamespaces() {
		$ns = $this->getWiki()->getNamespace( Namespaces::File );

		$this->filenamespaces = array( $ns['*'], $ns['canonical'] ) + $ns['aliases'];
	}

	/**
	 * Checks the given article to see if it starts with a file namespace
	 *
	 * @param $article
	 * @return bool
	 */
	protected function inFileNamespace( $article ) {
		foreach ( $this->filenamespaces as $ns ) {
			if ( strpos( $article, "$ns:" ) === 0 ) {
				return true;
			}
		}

		return false;
	}

	public function setPage( $page ) {
		$this->page = $page;
		return $this;
	}

	public function setUrl( $url ) {
		$this->url = $url;
		return $this;
	}
}