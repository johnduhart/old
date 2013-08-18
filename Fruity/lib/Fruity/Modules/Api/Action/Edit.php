<?php

namespace Fruity\Modules\Api\Action;

use Fruity\Modules\Api\BaseAction,
	Fruity\Modules\Api\ParameterCollector;

class Edit extends BaseAction {

	/**
	 * Title of the page to edit
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Text of the page
	 *
	 * @var string
	 */
	protected $text;

	/**
	 * Is the edit minor?
	 *
	 * @var bool
	 */
	protected $minor = false;

	/**
	 * Should the edit have a bot flag?
	 *
	 * @var bool
	 */
	protected $bot = true;

	/**
	 * Summary of the edit
	 *
	 * @var string
	 */
	protected $summary = '';

	/**
	 * What whatlist setting should this use
	 *
	 * @todo add constants for this
	 *
	 * @var string
	 */
	protected $watchlist = 'preferences';

	public function execute( ParameterCollector $parameters ) {
		$parameters
			->setAction( 'edit' )
			->setParameterRequired( 'title', $this->title )
			->setParameterRequired( 'text', $this->text )
			->setParameter( 'token', $this->api->getToken() )
			->setParameter( 'md5', md5( $this->text ) )
			->setParameterIf( 'watchlist', $this->watchlist )
			->setParameterIf( 'summary', $this->summary )
			->setParameterIf( 'minor', $this->minor )
			->setParameterIf( 'bot', $this->bot );
	}

	/**
	 * @param array $data
	 * @return EditResult
	 */
	public function processResult( array $data ) {
		return new EditResult( $data );
	}

	/**
	 * @param boolean $bot
	 * @return Edit
	 */
	public function setBot( $bot ) {
		$this->bot = $bot;
		return $this;
	}

	/**
	 * @param boolean $minor
	 * @return Edit
	 */
	public function setMinor( $minor ) {
		$this->minor = $minor;
		return $this;
	}

	/**
	 * @param string $summary
	 * @return Edit
	 */
	public function setSummary( $summary ) {
		$this->summary = $summary;
		return $this;
	}

	/**
	 * @param string $text
	 * @return Edit
	 */
	public function setText( $text ) {
		$this->text = $text;
		return $this;
	}

	/**
	 * @param string $title
	 * @return Edit
	 */
	public function setTitle( $title ) {
		$this->title = $title;
		return $this;
	}

	/**
	 * @param string $watchlist
	 * @return Edit
	 */
	public function setWatchlist( $watchlist ) {
		$this->watchlist = $watchlist;
		return $this;
	}


}
