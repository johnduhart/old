<?php

use \Fruity\Core;
use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command {
	protected function configure() {
		$this
			->setDescription( 'Runs the link bot' )
			->addArgument( 'host', InputArgument::REQUIRED, 'Host of the wiki to work on' )
			->addOption( 'url', null, InputOption::VALUE_OPTIONAL, 'What LinkSearch result to work off of' )
			->addOption( 'page', null, InputOption::VALUE_OPTIONAL, 'Page to process' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$host = $input->getArgument( 'host' );
		$config = Core::loadConfiguration( 'config.yml' );
		$config->wiki->baseurl = "http://$host/w/api.php";

		$wiki = Core::initWikiFromConfig( $config );
		$bot = new LinkBot( $wiki );

		if ( $url = $input->getOption( 'url' ) ) {
			$bot->setUrl( $url );
		} elseif ( $page = $input->getOption( 'page' ) ) {
			$bot->setPage( $page );
		}

		$bot->run();
	}

}
