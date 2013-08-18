<?php

namespace Fruity\Modules\Api\Query\M;

use Fruity\Modules\Api\Query\Base\M\BaseUserinfo,
	Fruity\Modules\Api\Query\QueryParameterCollector,
	Fruity\Modules\Api\Query\Result;

/**
 * Userinfo query module
 *
 * http://www.mediawiki.org/wiki/API:Meta#userinfo_.2F_ui
 *
 * @template Properties
 */
class Userinfo extends BaseUserinfo {
	const PROP_BLOCKINFO = 'blockinfo';
	const PROP_HASMSG = 'hasmsg';
	const PROP_GROUPS = 'groups';
	const PROP_RIGHTS = 'rights';
	const PROP_CHANGEABLEGROUPS = 'changeablegroups';
	const PROP_OPTIONS = 'options';
	const PROP_EDITCOUNT = 'editcount';
	const PROP_RATELIMITS = 'ratelimits';
	const PROP_EMAIL = 'email';
	//const PROP_REALNAME = 'realname'; // 1.18

	protected $validProperties = array(
		self::PROP_BLOCKINFO,
		self::PROP_HASMSG,
		self::PROP_GROUPS,
		self::PROP_RIGHTS,
		self::PROP_CHANGEABLEGROUPS,
		self::PROP_OPTIONS,
		self::PROP_EDITCOUNT,
		self::PROP_RATELIMITS,
		self::PROP_EMAIL,
	);

	/**
	 * @param \Fruity\Modules\Api\Query\QueryParameterCollector $parameters
	 * @return void
	 */
	public function execute( QueryParameterCollector $parameters ) {
		$parameters
			->addMeta( 'userinfo' )
			->setParameterIf( 'uiprop', $this->properties );
	}
}
