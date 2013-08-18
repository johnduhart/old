<?php

namespace FPChat\Plugin;

class Snarky extends AbstractPlugin
{
	public function onMention($bot, $line)
	{
		$msg = $line->message;

		if (preg_match('#(gay|faggot|homo|penis|dick|cock)#i', $msg))
		{
			return $bot->say($line->username . ': fuck off faggot');
		}

		if (preg_match('#^(hi(ya)?|hello|sup|hey)#i', $msg))
		{
			return $bot->say('screw you');
		}
	}
}
