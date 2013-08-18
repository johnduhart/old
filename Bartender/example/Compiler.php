<?php

namespace Example;

use Bartender\Compiler as BaseCompiler,
	Zend\CodeGenerator\Php\PhpClass;

class Compiler extends BaseCompiler {
	public function getBaseClass( PhpClass $class ) {
		if ( $class->getName() == 'BaseThing' ) {
			return '\Example\OtherBaseClass';
		} else {
			return parent::getBaseClass( $class );
		}
	}
}
