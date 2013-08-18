<?php

namespace Fruity\Modules\Api\Action;

class EditResult extends BaseResult {

	/**
	 * @var bool
	 */
	protected $noChange = false;

	/**
	 * @var string
	 */
	protected $newTimestamp = '';

	/**
	 * @var int
	 */
	protected $newRevId = 0;

	protected function loadFromApi( $data ) {
		$data = $data['edit'];

		if ( isset( $data['nochange'] ) ) {
			$this->noChange = true;
			return;
		}

		$this->newTimestamp = $data['newtimestamp'];
		$this->newRevId = $data['newrevid'];
	}

	/**
	 * @return int
	 */
	public function getNewRevId() {
		return $this->newRevId;
	}

	/**
	 * @return string
	 */
	public function getNewTimestamp() {
		return $this->newTimestamp;
	}

	/**
	 * @return boolean
	 */
	public function didChange() {
		return !$this->noChange;
	}
}
