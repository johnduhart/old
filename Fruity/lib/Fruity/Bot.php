<?php

namespace Fruity;

/**
 * Base class for creating robots
 */
abstract class Bot {

	/**
	 * @var \Fruity\Wiki
	 */
	private $wiki;

	/**
	 * @param Wiki $wiki
	 */
	public function __construct( Wiki $wiki ) {
		$this->wiki = $wiki;
	}

	/**
	 * @return Wiki
	 */
	public function getWiki() {
		return $this->wiki;
	}

	/**
	 * Function ran by Core to start the robot
	 */
	abstract public function run();
}
