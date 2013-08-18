<?php
namespace Fruity\Modules\Template;

use Fruity\Module;

/**
 * Template Module's definition class. Nothing much to see here.
 */
class TemplateModule implements Module {

	public static function initModule() {
		\Fruity\Core::requireModule( 'Page' );
	}
}

