<?php

namespace Fruity\Modules\User;

use Fruity\Modules\Api\Query\M\Userinfo,
	Fruity\Wiki;

/**
 * Represents a user from a Wiki
 */
class User {
	/**
	 * Wiki that this user is from
	 *
	 * @var \Fruity\Wiki
	 */
	protected $wiki;

	/**
	 * User's username
	 *
	 * @var string
	 */
	protected $username;

	/**
	 * User ID
	 *
	 * @var integer
	 */
	protected $id;

	/**
	 * Groups the user is in
	 *
	 * @var array
	 */
	protected $groups;

	/**
	 * Rights that the user has
	 *
	 * @var array
	 */
	protected $rights;

	public function __construct( Wiki $wiki ) {
		$this->wiki = $wiki;
	}

	/**
	 * Creates a user from the currently logged in user
	 *
	 * @param \Fruity\Wiki $wiki
	 * @return User
	 */
	public static function createFromCurrentUser( Wiki $wiki ) {
		$queryBuilder = $wiki->getApi()->getQueryBuilder();
		$userinfo = $queryBuilder->createMeta( 'userinfo' );
		$userinfo->setProperties(array(
			Userinfo::PROP_BLOCKINFO,
			Userinfo::PROP_RIGHTS,
			Userinfo::PROP_GROUPS,
		));
		$userData = $queryBuilder
			->add( $userinfo )
			->execute();

		$userData = $userData['query']['userinfo'];

		// We need to create a user by hand as we get the data from meta=userinfo
		$user = new self( $wiki );
		$user
			->setUsername( $userData['name'] )
			->setId( $userData['id'] )
			->setGroups( $userData['groups'] )
			->setRights( $userData['rights'] );

		return $user;
	}

	/**
	 * Sets the username of the User
	 *
	 * @param $username
	 * @return User
	 */
	public function setUsername( $username ) {
		$this->username = $username;
		return $this;
	}

	/**
	 * Gets the username of the user
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param array $groups
	 * @return User
	 */
	public function setGroups( $groups ) {
		$this->groups = $groups;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * @param int $id
	 * @return User
	 */
	public function setId( $id ) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param array $rights
	 * @return User
	 */
	public function setRights( $rights ) {
		$this->rights = $rights;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getRights() {
		return $this->rights;
	}
}
