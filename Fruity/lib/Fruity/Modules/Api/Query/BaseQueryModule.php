<?php

namespace Fruity\Modules\Api\Query;

use Fruity\Modules\Api\Api;

/**
 * Query Module abstract
 *
 * This is at the very bottom of the inheritance chain for QueryModules
 */
abstract class BaseQueryModule implements QueryModule {
	/**
	 * API that this builder belongs to
	 *
	 * @var \Fruity\Modules\Api\Api
	 */
	protected $api;

	/**
	 * Constructor
	 *
	 * @param \Fruity\Modules\Api\Api $api
	 */
	public function __construct( Api $api ) {
		$this->api = $api;
	}
}
