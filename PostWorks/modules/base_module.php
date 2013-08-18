<?php

class base_module {
	// Core class
	public $core;
	
	function __construct(&$core) {
		$this->core &= $core;
	}
}
