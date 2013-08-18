<?php

namespace FPChat\Plugin;

class Admin extends AbstractPlugin
{
	private $_admins;

	public function __construct($userId)
	{
		if (!is_array($userId))
		{
			$userId = array($userId);
		}

		$this->_admins = $userId;
	}

	/**
	 * Triggered on a command
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\Line $line
	 * @param string $command
	 * @param array $arguments
	 */
	public function onCommand($bot, $line, $command, $arguments)
	{
		if (!in_array($line->userId, $this->_admins))
		{
			return;
		}

		switch ($command)
		{
			case 'save':
				$bot->getPluginBroker()->onSave($bot);
				$bot->say($line->username . ': Data saved.');
				break;

			case 'stop':
			case 'die':
			case 'killurself':
				$bot->stop();
				break;
		}
	}

	public function onStop($bot)
	{
		$bot->say('Goodbye world!');
	}
}
