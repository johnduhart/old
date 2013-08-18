<?php

namespace Fruity\Modules\Api;

use Zend\Config\Config,
	Symfony\Component\Finder\Finder;

class ApiSelector {
	protected static $availableApis = array();

	/**
	 * @deprecated
	 * @param \Zend\Config\Config $config
	 * @return \Fruity\Modules\Api\Api
	 */
	public static function fetchApi( Config $config ) {
		return new Api( $config );
	}
}
