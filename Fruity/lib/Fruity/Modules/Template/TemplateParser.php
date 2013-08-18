<?php

namespace Fruity\Modules\Template;

class TemplateParser {
	const STATE_NAME = 0;
	const STATE_PARAMETERS = 1;

	protected static $stateFunctions = array(
		self::STATE_NAME => 'Name',
		self::STATE_PARAMETERS => 'Params',
	);

	/**
	 * Text of the template
	 *
	 * @var string
	 */
	protected $text;

	/**
	 * Length of the template text
	 *
	 * @var int
	 */
	protected $length;

	/**
	 * Position of the parser
	 *
	 * @var int
	 */
	protected $pos = 0;

	/**
	 * How deep the braces are
	 *
	 * @var int
	 */
	protected $braceDepth = 0;

	/**
	 * State name
	 *
	 * @var null|int
	 */
	protected $currentState = null;

	/**
	 * Data from the parser
	 *
	 * @var array
	 */
	protected $data = array(
		'name' => null,
		'params' => array(),
	);

    /**
     * Holds metadata about param (start and stop pos.)
     *
     * @var array
     */
    protected $paramMetadata = array();

	/**
	 * Last numbered parameter
	 *
	 * @var int
	 */
	protected $lastParamNumber = 1;

	public function __construct( $templateText ) {
		$this->text = $templateText;
	}

	public function parse() {
		if ( substr( $this->text, 0, 2 ) != '{{' ) {
			throw new \Exception( "Template text does not start with '{{'" );
		}

		$this->pos = 2;
		$this->currentState = self::STATE_NAME;
		$this->braceDepth = 2;
		$this->length = strlen( $this->text );

		$this->parseLoop();
	}

	protected function next() {
		if ( $this->atEnd() ) {
			\o::error( 'TemplateParser at end of string, cannot move forward any more', 1 );
			return;
		}
		$this->pos++;
	}

	protected function current() {
		return $this->text[$this->pos];
	}

	protected function peek() {
		return $this->text[ 1 + $this->pos ];
	}

	protected function atEnd() {
		return $this->pos == $this->length - 1;
	}

	protected function is( $char /*...*/ ) {
		return in_array( $this->current(), func_get_args() );
	}

	protected function goUntil( $chars ) {
		$chars = (array) $chars;

		while ( !call_user_func_array( array( $this, 'is' ), $chars ) && !$this->atEnd() ) {
			$this->next();
		}

		return !$this->atEnd();
	}

	protected function extractStr( $start, $end ) {
		$str = '';
		for ( $i = $start; $i <= $end; $i++ ) {
			$str .= $this->text[$i];
		}
		return $str;
	}

	protected function nextState() {
		$this->currentState++;
	}

	protected function parseLoop() {
		do {
			if ( $this->parseChar() ) {
				if ( isset( self::$stateFunctions[$this->currentState] ) ) {
					$func = 'handle' . self::$stateFunctions[$this->currentState];
					$this->$func();
				}
			}
			
			$this->next();
		} while( !$this->atEnd() );
	}

	/**
	 * Parse individual characters
	 *
	 * @return bool Returns true if parsing can continue
	 */
	protected function  parseChar() {
		if ( $this->is( '}', '{' ) ) {
			if ( $this->is( '{' ) ) {
				$this->braceDepth++;
			} else {
				$this->braceDepth--;
			}
			return false;
		}

		return true;
	}

	protected function handleName() {
		\o::debug( 'Handling name' );
		$name = '';

		do {
			// TODO: Brace checking?
			/*if ( $this->is( '|' ) ) {
				break;
			}*/

			if ( $this->isWhitespace() ) {
				while ( $this->isWhitespace( $this->peek() ) ) {
					$this->next();
				}
				$name .= ' ';
			} else {
				$name .= $this->current();
			}

			if ( $this->peek() == '|' || $this->peek() == '}' ) {
				break;
			}

			$this->next();
		} while ( true );

		$name = trim( $name );
		$this->data['name'] = $name;
		
		$this->nextState();
	}

	protected function handleParams() {
		if ( !$this->goUntil( '|' ) ) {
			// Oops, no params for me to handle
			return $this->nextState();
		}
		\o::debug( 'Handling params' );

		do {
			$startPos = $this->pos;
			$this->next();

			while ( $this->peek() != '|' ) {
				$this->next();

				// TODO: If we encounter templates in templates in templates we're screwed
				if ( $this->is( '{' ) && $this->peek() == '{' ) {
					$this->braceDepth++;
					while ( $this->braceDepth > 2 ) {
						$this->next();

						if ( $this->is( '{' ) ) {
							$this->braceDepth++;
						} elseif ( $this->is( '}' ) ) {
							$this->braceDepth--;
						}
					}
				}
				if ( $this->is( '[' ) && $this->peek() == '[' ) {
					while ( !$this->is( ']' ) && $this->peek() != ']' ) {
						$this->next();
					}
				}

				if ( $this->pos == ( $this->length - 3 ) ) {
					break;
				}
			}

			$endPos = $this->pos;
			$this->handleParam( $startPos + 1, $endPos );
			$this->pos= $endPos;

			if ( $this->peek() == '|' ) {
				$this->next();
			}

			if ( $this->pos == ( $this->length - 3 ) ) {
				break;
			}
		} while( true );
	}

	protected function handleParam( $startPos, $endPos ) {
		$metadata = array(
		    'start' => $startPos,
		    'stop' => $endPos,
		);
		$paramStr = $this->extractStr( $startPos, $endPos );

		if ( strpos( $paramStr, '=' ) === false ) {
			// Numbered parameter
			return $this->handleNumberParam( $paramStr, $metadata );
		}
		
		// Might be a named param, parse it out
		$name = $value = '';
		$max = strlen( $paramStr ) - 1;
		for ( $i = 0; $i <= $max; $i++ ) {
			$char = $paramStr[$i];

			// If we're encountering a template at this point it's simply not
			// possible to be a named parameter. Abort.
			if ( $char == '{' && $paramStr[$i + 1] == '{' ) {
				return $this->handleNumberParam( $paramStr, $metadata );
			}

			if ( $char == '=' ) {
				$i++;
				break;
			}
			$name .= $char;
		}

		list( $nameStart, $nameStop ) = $this->determineTruePosition( $name, $startPos, ( $startPos + $i ) - 1 );
		$metadata['namestart'] = $nameStart;
		$metadata['namestop'] = $nameStop;

		$value = substr( $paramStr, $i );
		list( $valueStart, $valueEnd ) = $this->determineTruePosition( $value, $startPos + $i, $endPos );
		$metadata['valuestart'] = $valueStart;
		$metadata['valuestop'] = $valueEnd;

		$trimmedName = trim( $name );
		$this->data['params'][$trimmedName] = trim( $value );
		$this->paramMetadata[$trimmedName] = $metadata;
	}

	protected function handleNumberParam( $str, $metadata ) {
		list( $valueStart, $valueEnd ) = $this->determineTruePosition( $str, $metadata['start'], $metadata['stop'] );
		$metadata['valuestart'] = $valueStart;
		$metadata['valuestop'] = $valueEnd;


		$this->data['params'][$this->lastParamNumber] = trim( $str );
		$this->paramMetadata[$this->lastParamNumber] = $metadata;
		$this->lastParamNumber++;
	}

	/**
	 * Checks if the given string is whitespace
	 *
	 * @param string $str Defaults to current char
	 * @return bool
	 */
	protected function isWhitespace( $str = null ) {
		if ( $str === null ) {
			$str = $this->current();
		}

		return trim( $str ) === '';
	}

    /**
     * Determines the actual position of the string
     *
     * @param $str String to check
     * @param $start int pos in the full string
     * @param $stop int pos in the full string
     * @return array
     */
    protected function determineTruePosition( $str, $start, $stop ) {
        $len = strlen($str);
        $leftPadding = $len - strlen( ltrim( $str ) );
        $rightPadding = $len - strlen( rtrim( $str ) );

        return array(
            $start + $leftPadding,
            $stop - $rightPadding
        );
    }

	/**
	 * @return array
	 */
	public function getData() {
		print_r($this->paramMetadata);
		return $this->data;
	}
}
