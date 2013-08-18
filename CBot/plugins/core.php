<?php

function core_load() {
	global $CBot;
	$CBot->hooks->addHook('line', 'core_line');
	$CBot->hooks->addHook('privmsg_chancmds', 'core_cmd');
}

function core_unload() {
	global $CBot;
	$CBot->hooks->removeHook('line', 'core_line');
	$CBot->hooks->removeHook('privmsg_chancmds', 'core_cmd');
}

function core_line($line) {
	echo $line."\n";
}

function core_cmd($nick, $target, $message, $host, $cmd, $arg) {
	global $CBot, $botfile;
	switch($cmd) {
		case 'die':
			if($CBot->auth->checkPermission($nick, $host, 'die')) {
				$CBot->run = false;
				$CBot->irc->run = false;
				$CBot->irc->quit('Bye');
			}
			break;
		case 'reboot':
			if($CBot->auth->checkPermission($nick, $host, 'reboot')) {
				$CBot->run = false;
				$CBot->irc->run = false;
				$CBot->irc->quit('Rebooting, brb...');
				exec("screen -dmS " . $botfile . ' php ' . $botfile . '.php');
			}
			break;
		case 'join':
			if($CBot->auth->checkPermission($nick, $host, 'join')) {
				$CBot->irc->join($arg);
			}
			break;
		case 'joink':
			if($CBot->auth->checkPermission($nick, $host, 'joink')) {
				$tmp = explode(' ', $arg, 2);
				$CBot->irc->join($tmp[0], $tmp[1]);
			}
			break;
		case 'leave':
		case 'part':
			if($CBot->auth->checkPermission($nick, $host, 'part')) {
				$tmp = explode(' ', $arg, 2);
				$CBot->irc->part($tmp[0], $tmp[1]);
			}
			break;
		case 'invite':
			if($CBot->auth->checkPermission($nick, $host, 'invite')) {
				$CBot->irc->invite($target, $arg);
			}
			break;
		case 'kick':
			if($CBot->auth->checkPermission($nick, $host, 'kick')) {
				$tmp = explode(' ', $arg, 2);
				$CBot->irc->kick($target, $tmp[0], $tmp[1]);
			}
			break;
		case 'mode':
			if($CBot->auth->checkPermission($nick, $host, 'mode')) {
				$CBot->irc->mode($target, $arg);
			}
			break;
		case 'say':
			if($CBot->auth->checkPermission($nick, $host, 'say')) {
				$CBot->irc->say($target, $arg);
			}
			break;
		case 'load':
			if($CBot->auth->checkPermission($nick, $host)) {
				$CBot->hooks->load($arg);
			}
			break;
		case 'unload':
			if($CBot->auth->checkPermission($nick, $host)) {
				$CBot->hooks->unload($arg);
			}
			break;
		case 'eval':
			if($CBot->auth->checkPermission($nick, $host)) {
				eval($arg);
			}
			break;
		case 'nick':
			if($CBot->auth->checkPermission($nick, $host)) {
				$CBot->irc->nick($arg);
			}
			break;
		case 'authdebug':
			$CBot->auth->debug();
			echo "Auth ".$CBot->auth->checkPermission($nick, $host)."\n";
			break;
	}
}
