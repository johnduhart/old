<?php

namespace FPChat\Plugin;
use FPChat\Plugin\Logs\User;
 
class LogQuery extends AbstractPlugin
{
	/**
	 * Triggered on a chat line
	 *
	 * @param \FPChat\Bot $bot
	 * @param \FPChat\Line $line
	 */
	public function onLine($bot, $line)
	{
		if (substr($line->message, 0, 1) != '.') return;

		list($command) = explode(' ', substr($line->message, 1));

		switch ($command)
		{
			case 'info':
				$username = substr($line->message, 6);
				$user = User::one(array('username' => $username));

				if ($user === null)
				{
					$bot->say($line->username . ': I\'ve never seen that user before!');
					return;
				}

				$l = $user->lines > 0 ? $user->lines : 0;
				$lS = $l == 1 ? '' : 's';
				$bot->say($line->username . ': ' . $user->username . ' has said ' . $l . ' different thing'.$lS);
				break;
		}
	}
}
