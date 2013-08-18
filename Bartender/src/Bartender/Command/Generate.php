<?php

namespace Bartender\Command;

use Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Command\Command,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command {
	protected function configure() {
		$this
			->setName( 'generate' )
			->setDescription( 'Generates any injection classes needed' )
			->setDefinition(array(
				new InputArgument( 'config', InputArgument::REQUIRED, 'Bartender configuration file' )
			));
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$configFile = $input->getArgument( 'config' );

		if ( !is_readable( $configFile ) ) {
			throw new \RuntimeException( 'Invalid configuration file given' );
		}

		switch ( pathinfo( $configFile, PATHINFO_EXTENSION ) ) {
			case 'yml':
			case 'yaml':
				// The ability to mix and match is awesome :)
				$config = new \Zend\Config\Yaml( $configFile, null,
					array( 'yaml_decoder' => array( 'Symfony\\Component\\Yaml\\Yaml', 'parse' ) ) );
				break;

			case 'xml':
			case 'ini':
			case 'json':
				$class = '\\Zend\\Config\\' . ucfirst( pathinfo( $configFile, PATHINFO_EXTENSION ) );
				$config = new $class( $configFile );
				break;

			default:
				throw new \InvalidArgumentException( 'Unsupported configuration file passed to ' . __METHOD__ );
		}

		if ( $config->compiler !== null ) {
			$compilerClass = $config->compiler;
			require_once pathinfo( $configFile, PATHINFO_DIRNAME ) . '/' . $config->compiler_file;
		} else {
			$compilerClass = 'Bartender\Compiler';
		}

		$compiler = new $compilerClass();
		$compiler
			->setCliOutput( $output )
			->setRootDir( pathinfo( $configFile, PATHINFO_DIRNAME ) )
			->setFolder( 'interface', $config->interfaces )
			->setFolder( 'templates', $config->templates )
			->setFolder( 'output', $config->generated )
			->setScanDir( $config->classes )
			->setBaseClass( $config->abstract )
			->setNamespace( $config->namespace )
			->setDefaultTemplates( ( isset( $config->default_templates ) ) ? $config->default_templates->toArray() : array() )
			// And away we go
			->compile();
	}
}
