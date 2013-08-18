<?php

namespace Example\Templates;

class Dog {
	const TYPE_A = 'a';
	const TYPE_B = 'b';

	protected $name;

	public $woof = 'woof';

	function getDog( $name, $type ) {
		$dogA = $name;
		$this->name = $name;
		$type = self::TYPE_A;
	}

	function down() {
		$this->heel();
	}

	function heel() {
		// Do some stuff
		echo 'Heeling';
	}

	private function nap() {
		echo 'Napping....';
	}
}
