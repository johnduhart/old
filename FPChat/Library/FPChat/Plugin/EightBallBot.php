<?php

namespace FPChat\Plugin;

class EightBallBot extends AbstractPlugin
{
	public $replies = array(
		'As I see it, yes',
		'It is certain',
		'It is decidedly so',
		'Most likely',
		'Outlook good',
		'Signs point to yes',
		'Without a doubt',
		'Yes',
		'Yes - definitely',
		'You may rely on it',
		'Reply hazy, try again',
		'Ask again later',
		'Better not tell you now',
		'Cannot predict now',
		'Concentrate and ask again',
		'Don\'t count on it',
		'My reply is no',
		'My sources say no',
		'Outlook not so good',
		'Very doubtful'
	);
	
	public function onLine($bot,$line)
	{
		$msg = $line->message;

		if (substr($msg,0,7)=='@8ball ')
		{
			$reply = '8Ball: @'.$line->username . ': '.$this->replies[rand(0, count($this->replies) - 1)];
			return $bot->say($reply);
		}
	}

	public function onPostSetup($bot){
		echo 'onConnect ran'."\r\n";

		$bot->say('8BallBot usage: Say "@8ball <your question>".');
	}
}