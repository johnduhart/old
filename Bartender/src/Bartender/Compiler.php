<?php

namespace Bartender;

use Symfony\Component\Console\Output\OutputInterface,
	Symfony\Component\Finder\Finder,
	pdepend\reflection\ReflectionSession,
	pdepend\reflection\factories\NullReflectionClassFactory,
	Zend\CodeGenerator\Php\PhpFile,
	Zend\CodeGenerator\Php\PhpClass,
	Zend\Reflection\ReflectionDocblock;

class Compiler {
	/**
	 * Output interfaces for outputting status to the command line
	 *
	 * @var \Symfony\Component\Console\Output\OutputInterface
	 */
	protected $outputInterface;

	/**
	 * Folder to search for key components
	 *
	 * @var array
	 */
	protected $folders = array(
		'output' => 'Base',
		'template' => 'Templates',
	);

	/**
	 * Base folder for all operations
	 *
	 * @var string
	 */
	protected $rootDir;

	/**
	 * Directory of where to look for files
	 *
	 * @var string
	 */
	protected $scanDir;

	/**
	 * Class that every generated mixin should extend
	 *
	 * @var string
	 */
	protected $baseClass = null;

	/**
	 * Array of templates and phpFiles
	 *
	 * @var array
	 */
	protected $templates = array();

	/**
	 * Base namespace name to do the serving from
	 *
	 * @var null|string
	 */
	protected $namespace = null;

	/**
	 * Default templates
	 *
	 * @var array
	 */
	protected $defaultTemplates = array();

	protected function output( $message = '' ) {
		if ( $this->outputInterface ) {
			$this->outputInterface->writeln( $message );
		}
	}

	/**
	 * sprintf's the class name and file path together, and outputs it
	 *
	 * @param $className
	 * @param $fileName
	 * @return void
	 */
	protected function outputClassAndFile( $className, $fileName ) {
		$this->output( sprintf( '  %s (%s)', $className, $fileName ) );
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @return Compiler
	 */
	public function setCliOutput( OutputInterface $output ) {
		$this->outputInterface = $output;
		return $this;
	}

	/**
	 * @param string $rootDir
	 * @return Compiler
	 */
	public function setRootDir( $rootDir ) {
		$this->rootDir = realpath( $rootDir ) . '/';
		return $this;
	}

	/**
	 * @param string $folderName
	 * @param string $value
	 * @return Compiler
	 */
	public function setFolder( $folderName, $value ) {
		$folderName = strtolower( $folderName );
		if ( array_key_exists( $folderName, $this->folders ) && $value !== null ) {
			$this->folders[ $folderName ] = $value;
		}
		return $this;
	}

	/**
	 * Returns a folder
	 *
	 * @throws \RuntimeException
	 * @param $folderName
	 * @return string
	 */
	public function getFolder( $folderName ) {
		$folderName = strtolower( $folderName );
		if ( array_key_exists( $folderName, $this->folders ) ) {
			return $this->rootDir . $this->folders[ $folderName ];
		} else {
			throw new \RuntimeException( 'Invalid folder type passed to ' . __METHOD__ );
		}
	}

	/**
	 * Gets the appropriate namespace for a folder
	 *
	 * @throws \RuntimeException
	 * @param $folderName
	 * @return mixed
	 */
	public function getFolderNamespace( $folderName ) {
		$folderName = strtolower( $folderName );
		if ( array_key_exists( $folderName, $this->folders ) ) {
			return str_replace( '/', '\\', $this->folders[ $folderName ] );
		} else {
			throw new \RuntimeException( 'Invalid folder type passed to ' . __METHOD__ );
		}
	}

	/**
	 * @todo Check to see if the scan dir is the root directory
	 *
	 * @param string $scanDir
	 * @return Compiler
	 */
	public function setScanDir( $scanDir ) {
		$this->scanDir = $scanDir;
		return $this;
	}

	public function getScanDir() {
		return $this->rootDir . $this->scanDir;
	}

	/**
	 * @param string $baseClass
	 * @return Compiler
	 */
	public function setBaseClass( $baseClass ) {
		$this->baseClass = $baseClass;
		return $this;
	}

	/**
	 * Sets the namespace
	 *
	 * @param $namespace
	 * @return Compiler
	 */
	public function setNamespace( $namespace ) {
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * Sets default templates to apply to all generated classes
	 *
	 * @param array $templates
	 * @return Compiler
	 */
	public function setDefaultTemplates( array $templates ) {
		$this->defaultTemplates = $templates;
		return $this;
	}

	/**
	 * Returns a fully qualified BaseClass
	 *
	 * @param \Zend\CodeGenerator\Php\PhpClass $class
	 * @return null|string
	 */
	public function getBaseClass( PhpClass $class ) {
		if ( $this->namespace === null || substr( $this->baseClass, 0, 1 ) == '\\' ) {
			return $this->baseClass;
		}

		// Check to see if there's a \ in there already
		if ( strstr( $this->baseClass, '\\' ) ) {
			return '\\' . $this->baseClass;
		} else {
			return $this->namespace . '\\' . $this->baseClass;
		}
	}

	/**
	 * Finds php files in a folder type defined in $this->folderTypes
	 *
	 * @param string $folderName
	 * @return \Symfony\Component\Finder\Finder
	 */
	protected function phpFilesInFolder( $folderName ) {
		return $this->phpFilesInPath( $this->getFolder( $folderName ) );
	}

	/**
	 * Finds php files in $path
	 *
	 * @param string $path
	 * @return \Symfony\Component\Finder\Finder
	 */
	protected function phpFilesInPath( $path ) {
		return Finder::create()
			->in( $path )
			->files()
			->ignoreDotFiles( true )
			->ignoreVCS( true )
			->name( '*.php' );
	}

	/**
	 * @return \pdepend\reflection\queries\ReflectionFileQuery
	 */
	protected function getFileQuery() {
		$s = new ReflectionSession();
		$s->addClassFactory( new NullReflectionClassFactory() );
		return $s->createFileQuery();
	}

	public function compile() {
		$this->output( 'Loading templates' );
		$this->loadTemplates();
		$this->output();

		$this->output( 'Examining classes' );
		$this->examineClasses();
		$this->output();
	}

	/**
	 * Loads the template classes from their defined directory
	 *
	 * @todo Multiple templates in one file?
	 *
	 * @return void
	 */
	protected function loadTemplates() {
		$templatesFiles = $this->phpFilesInFolder( 'template' );

		foreach ( $templatesFiles as $file ) {
			$phpFile = PhpFile::fromReflectedFileName( $file );
			$class = $phpFile->getClass();
			$this->templates[ $class->getName() ] = $class;
			$this->outputClassAndFile( $class->getName(), $file->getPathname() );
		}
	}

	/**
	 * Analyzes classes to see if they need a base class
	 *
	 * @return void
	 */
	protected function examineClasses() {
		$classFiles = $this->phpFilesInPath( $this->getScanDir() );
		$fileQuery = $this->getFileQuery();

		/** @var \Symfony\Component\Finder\SplFileInfo $file */
		foreach ( $classFiles as $file ) {
			/** @var \ReflectionClass $class */
			foreach ( $fileQuery->find( $file ) as $class ) {
				$this->outputClassAndFile( $class->getName(), $file->getPathname() );

				if ( $class->getParentClass() === false ) {
					$this->output( '    Non-extending class, skipping' );
					continue;
				}

				$baseName = $class->getParentClass()->getName();
				$targetBaseName =  'Base' . $class->getShortName();
				$path = '';
				if ( $this->namespace !== null ) {
					//( $this->namespace !== null ? $this->namespace . '\\' . $this->folders['output'] . '\\' : '' )
					$namespace = $this->namespace . '\\' . $this->getFolderNamespace( 'output' );
					if ( $file->getRelativePath() != '' ) {
						$namespace .= '\\' . str_replace( '/', '\\', $file->getRelativePath() );
						$path = $file->getRelativePath() . '/';
					}

					$targetBaseName = $namespace . '\\' . $targetBaseName;
				}
				
				if ( $baseName != $targetBaseName ) {
					$this->output( '    Non-based class, skipping' );
					continue;
				}

				$templates = array();
				if ( ( $docBlock = $class->getDocComment() ) !== false ) {
					$docBlock = new ReflectionDocblock( $docBlock );
					foreach ( $docBlock->getTags( 'template' ) as $tag ) {
						$template = $tag->description;
						$this->output( '    Uses ' . $template );
						$templates[] = $template;
					}
				}

				$this->buildBaseClass( 'Base' . $class->getShortName(), $templates, $namespace, $path );
			}
		}
	}

	/**
	 * Builds a base class
	 *
	 * @param string $className
	 * @param array $templates
	 * @param string $path
	 * @return void
	 */
	protected function buildBaseClass( $className, $templates, $namespace, $path ) {
		static $lastTemplateClass = array();
		$templates = array_merge( $templates, $this->defaultTemplates );
		$class = new PhpClass();
		$class
			->setName( $className )
			->setAbstract( true )
			->setNamespaceName( $namespace );

		foreach ( $templates as $template ) {
			/** @var \Zend\CodeGenerator\Php\PhpClass $templateClass  */
			if ( !isset( $this->templates[$template] ) ) {
				$this->output( '   Invalid template ' . $template );
				continue;
			}
			$templateClass = $this->templates[$template];

			// Loop through docblocks to replace references
			// to the template class
			/** @var \Zend\CodeGenerator\Php\PhpMethod $method */
			foreach ( $templateClass->getMethods() as $method ) {
				$docblock = $method->getDocblock();
				if ( $docblock === null ) {
					continue;
				}
				if ( !isset( $lastTemplateClass[$template] ) ) {
					$lastTemplateClass[$template] = $template;
				}
				$docblock->setSourceContent( str_replace( $lastTemplateClass[$template], $className, $docblock->getSourceContent() ) );
			}
			$lastTemplateClass[$template] = $className;

			$class
				->setConstants( (array) $templateClass->getConstants() )
				->setProperties( (array) $templateClass->getProperties() )
				->setMethods( (array) $templateClass->getMethods() );
		}

		$class->setExtendedClass( $this->getBaseClass( $class ) );

		$file = new PhpFile();
		$file
			->setClass( $class )
			->setDocblock( 'This file was generated by bartender and should not be modified' );

		$this->output( '   Generating ' . $className );

		// Have a path? make the folder
		$path = $this->getFolder( 'output' ) . '/' . $path;
		if ( !is_dir( $path ) ) {
			mkdir( $path );
		}

		file_put_contents( $path . $className . '.php', $file->generate() );
	}

}
