<?php

namespace FPChat\Plugin;

class ConsolePrint extends AbstractPlugin
{
	public function onLine($bot, $line)
	{
		echo '<' . $line->username . '> ' . $line->message . "\n";
	}

	public function onQuit($bot, $user)
	{
		echo "* " . $user->username . " quit\n";
	}

	public function onJoin($bot, $user)
	{
		echo "* " . $user->username . " joined\n";
	}
}
