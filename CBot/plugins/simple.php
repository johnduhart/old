<?php

function simple_load() {
	global $CBot;
	$CBot->hooks->addHook('privmsg_chancmds', 'simple_cmd');
}

function simple_unload() {
	global $CBot;
	$CBot->hooks->removeHook('privmsg_chancmds', 'simple_cmd');
}

function simple_cmd($nick, $target, $message, $host, $cmd, $arg) {
	global $CBot;
	switch($cmd) {
		case 'test':
			$CBot->irc->say($target, 'Test test, is this thing on?');
			break;
		case 'cbot':
			$CBot->irc->say($target, 'I am CWiiBotv2, hear me rawr.');
			break;
	}
}