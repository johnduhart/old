<?php
/**
 * This is a utility file used to generate mixin classes
 *
 * It does nothing during normal operation of Fruity
 *
 * @ignore
 */

use Zend\CodeGenerator\Php\PhpClass;

/**
 * Utility class to generate mixin files
 *
 * @ignore
 */
class BartenderCompiler extends Bartender\Compiler {
	/**
	 * Override Bartender's getBaseClass function with our own to modify what
	 * class the generated files extend
	 *
	 * @param Zend\CodeGenerator\Php\PhpClass $class
	 * @return string
	 */
	/*public function getBaseClass( PhpClass $class ) {
		// Strip off the base portion
		$name = substr( $class->getName(), 4);
		return '\\' . str_replace(
				array( 'HttpApi', '\\Base\\' ),
				array( 'BaseApi', '\\' ),
				$class->getNamespaceName()
			) . '\\' . $name;
	}*/
}
