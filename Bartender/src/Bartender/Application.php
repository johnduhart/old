<?php

namespace Bartender;

use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication {
	public function __construct() {
		parent::__construct( 'Bartender', 'Dev' );

		$this->addCommands(array(
			new Command\Generate(),
		));
	}
}
