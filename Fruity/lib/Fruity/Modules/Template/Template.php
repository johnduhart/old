<?php

namespace Fruity\Modules\Template;

use Fruity\Modules\Page\Page;

/**
 * Class for handling templates
 */
class Template {

	/**
	 * Cache of template aliases
	 *
	 * @var array
	 */
	protected static $aliasCache = array();

	/**
	 * Text representing the template
	 *
	 * @var string
	 */
	protected $templateText;

	/**
	 * Name of the template
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Parameters to the template
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 * Template parser
	 *
	 * @var TemplateParser
	 */
	protected $parser;

	/**
	 * Page object this template belongs to
	 *
	 * @var Page
	 */
	protected $page;

	/**
	 * Template's position in the page
	 *
	 * @var int
	 */
	protected $pagePos;

	/**
	 * @param string $templateText
	 * @param \Fruity\Modules\Page\Page $page
	 * @param int $pagePos
	 */
	public function __construct( $templateText, $page = null, $pagePos = null ) {
		$this->templateText = $templateText;
		$this->page = $page;
		$this->pagePos = $pagePos;
		$this->parser = new TemplateParser( $templateText );
		$this->parse();
	}

	/**
	 * Retrieves aliases for a template
	 *
	 * @param string $templateName
	 * @param \Fruity\Wiki $wiki
	 * @return array
	 */
	public static function getAliases( $templateName, \Fruity\Wiki $wiki ) {
		if ( isset( self::$aliasCache[$templateName] ) ) {
			return self::$aliasCache[$templateName];
		}

		$result = $wiki->getPageRedirects( 'Template:' . $templateName, 10 );
		$aliases = array( $templateName );

		foreach ( $result as $page ) {
			$aliases[] = $page->getUnprefixedTitle();
		}

		self::$aliasCache[$templateName] = $aliases;

		return $aliases;
	}

	/**
	 * Gets a Template instance
	 *
	 * @param string $templateName
	 * @param string $text
	 * @param \Fruity\Modules\Page\Page $page
	 * @return bool|Template
	 */
	public static function templateInText( $templateName, $text, $page = null ) {
		// TODO: This doesn't cover namespaces outside of the template namespace
		if ( is_array( $templateName ) ) {
			$templateName = array_map( 'preg_quote', $templateName, array( '/' ) );
			$regex = '(' . implode( '|', $templateName ) . ')';
		} else {
			$regex = preg_quote( $templateName, '/' );
		}
		$r = preg_match( "/{{([^a-z0-9\-_!]+)?(Template:)?$regex/i", $text, $matches );

		// No results?
		if ( $r === 0 ) {
			// TODO: Exception?
			\o::debug( 'Nothing' );
			return false;
		}

		// TODO: Multiple matches
		$match = $matches[0];
		$pos = strpos( $text, $match );

		\o::debug( "Found template at pos $pos" );

		// Extract the template text
		$templateText = $match;
		$openBraces = 2;
		for( $i = $pos + strlen( $match ); $i < strlen( $text ); $i++ ) {
			$char = $text[$i];
			$templateText .= $char;

			switch ( $char ) {
				case '{':
					$openBraces++;
					break;
				case '}':
					$openBraces--;
			}

			if ( $openBraces == 0 ) {
				break;
			}
		}

		\o::debug( "Template text: $templateText" );

		return new self( $templateText, $page, $pos );
	}

	/**
	 * Parses the template
	 */
	public function parse() {
		$this->parser->parse();
		$data = $this->parser->getData();

		$this->name = $data['name'];
		$this->parameters = $data['params'];
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getName();
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * @return string
	 */
	public function getTemplateText() {
		return $this->templateText;
	}

	/**
	 * Get a parameter value
	 *
	 * @param $param
	 * @return mixed
	 */
	public function getParameter( $param ) {
		return $this->parameters[trim( $param )];
	}

	/**
	 * Removes the template from the page
	 */
	public function remove() {
		if ( $this->page === null ) {
			throw new \Exception( 'The template does not have a Page associated with it' );
		}

		$length = strlen( $this->templateText );
		$this->page->_replaceRange( '', $this->pagePos, $this->pagePos + $length, $this );
	}
}
