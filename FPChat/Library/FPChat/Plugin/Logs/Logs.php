<?php

namespace FPChat\Plugin\Logs;
use FPChat\Plugin\AbstractPlugin;

class Logs extends AbstractPlugin
{
	const UPDATE_INTERVAL = 10;

	private $_users = array();

	private $_nextUpdateTick = 0;

	/**
	 * Triggered after setup
	 *
	 * @param \FPChat\Bot $bot
	 */
	public function onPostSetup($bot)
	{
		// Start by retrieving the names of currently logged in users
		$names = $bot->getNames();

		foreach ($names AS $userid => $info)
		{
			$user = $this->_getUser($userid, $info->username);

			// TODO: Last seen, time tracking etc

			$this->_users[$userid] = $user;
		}

		$this->_nextUpdateTick = time() + self::UPDATE_INTERVAL;
	}

	/**
	 * Triggered on quit
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\User $user
	 */
	public function onQuit($bot, $user)
	{
		$this->_ensureUser($user->id, $user->username);

		$bit = new Bit(array(
			'timestamp' => time(),
			'type' => 'quit',
			'user' => $this->_users[$user->id]
		));
		$bit->save();

		// Save any data and remove the class
		$this->_users[$user->id]->lastseen = time();
		$this->_users[$user->id]->save();

		unset($this->_users[$user->id]);
	}

	/**
	 * Triggered on join
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\User $user
	 */
	public function onJoin($bot, $user)
	{
		$this->_ensureUser($user->id, $user->username);

		$bit = new Bit(array(
			'timestamp' => time(),
			'type' => 'join',
			'user' => $this->_users[$user->id]
		));
		$bit->save();
	}

	/**
	 * Triggered on a chat line
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\Line $line
	 */
	public function onLine($bot, $line)
	{
		$this->_ensureUser($line->userId, $line->username);

		$bit = new Bit(array(
			'timestamp' => $line->timestamp,
			'type' => 'chat',
			'message' => $line->message
		));

		$bit->user = $this->_users[$line->userId];

		$bit->save();

		$this->_users[$line->userId]->inc('lines', 1);
		$this->_users[$line->userId]->lastspoke = $line->timestamp;
	}

	/**
	 * Triggered each tick after everything else
	 *
	 * @param \FPChat\Bot $bot
	 */
	public function onPostTick($bot)
	{
		if ($this->_nextUpdateTick > time())
		{
			return;
		}

		$this->save();

		$this->_nextUpdateTick = time() + self::UPDATE_INTERVAL;
	}

	/**
	 * Forces a data save
	 *
	 * @param $bot
	 * @return void
	 */
	public function onSave($bot)
	{
		$this->save();
	}

	/**
	 * Forces a data save
	 *
	 * @param $bot
	 * @return void
	 */
	public function onStop($bot)
	{
		$this->save();
	}

	/**
	 * Saves all the current data
	 */
	public function save()
	{
		$time = time();

		foreach ($this->_users AS $userid => &$user)
		{
			$user->lastseen = $time;
			$user->save();
		}
	}

	/**
	 * Returns a new User instance
	 *
	 * @param  $userId
	 * @param  $username
	 * @return User
	 */
	private function _getUser($userId, $username)
	{
		$user = User::one(array('userid' => $userId));

		// Make a new user if one doesn't exist
		if ($user === null)
		{
			$user = new User();
			$user->userid = $userId;
			$user->username = $username;
		}

		$user->lastseen = time();
		$user->save();

		return $user;
	}

	/**
	 * Makes sure a user is initialized
	 *
	 * @param $userId
	 * @return void
	 */
	private function _ensureUser($userId, $username)
	{
		if (!isset($this->_users[$userId]))
		{
			$this->_users[$userId] = $this->_getUser($userId, $username);
		}
	}
}
