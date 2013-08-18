<?php

$cwiibot_messages = array();
$cwiibot_ccrmessages = array();

function cwiibot_m_load() {
	global $CBot, $cwiibot_messages, $cwiibot_ccrmessages, $next_m_update;
	if(!$CBot->db->table_exists('messages')) {
		$CBot->db->query("
			CREATE TABLE `{$CBOT->db->tabe_prefix}messages` (
			`id` INT (20) NOT NULL AUTO_INCREMENT,
			`trigger` VARCHAR (20) NOT NULL,
			`message` TEXT NOT NULL,
			PRIMARY KEY(`id`))");
	}
	
	$cwiibot_messages = cwiibot_m_getMessages();
	$cwiibot_ccrmessages = cwiibot_m_getCCRMessages();
	$next_m_update = time() + 900;
	
	$CBot->hooks->addHook('privmsg_chancmds', 'cwiibot_m_cmd');
	$CBot->scheduler->addEvent('cwiibot_m_reload', $next_m_update);
}

function cwiibot_m_unload() {
	global $CBot, $cwiibot_messages, $cwiibot_ccrmessages, $next_m_update;
	$CBot->hooks->removeHook('privmsg_chancmds', 'cwiibot_m_cmd');
	
	$cwiibot_messages = array();
	$cwiibot_ccrmessages = array();
	
	$CBot->scheduler->removeEvent('cwiibot_m_reload', $next_m_update);
}

function cwiibot_m_getMessages() {
	global $CBot;
	$query = $CBot->db->simple_select('messages');
	$ret = array();
	while ($x = $CBot->db->fetch_array($query)) {
			$ret[$x['trigger']] = $x['message'];
	}
	return $ret;
}

function cwiibot_m_getCCRMessages() {
	global $CBot;
	$query = $CBot->db->simple_select('messages', 'message', '`trigger` = \'ccr\'');
	$ret = array();
	while ($x = $CBot->db->fetch_array($query)) {
			$ret[] = $x['message'];
	}
	return $ret;
}

function cwiibot_m_reload() {
	global $CBot, $cwiibot_messages, $cwiibot_ccrmessages, $next_m_update;
	
	echo "Messages automaticlly reloaded\n";
	
	$cwiibot_messages = cwiibot_m_getMessages();
	$cwiibot_ccrmessages = cwiibot_m_getCCRMessages();
	
	$next_m_update = time() + 900;
	$CBot->scheduler->addEvent('cwiibot_m_reload', $next_m_update);
}

function cwiibot_m_cmd($nick, $target, $message, $host, $cmd, $arg) {
	global $CBot, $cwiibot_messages, $cwiibot_ccrmessages;
	if($cmd == 'debugm') {
		print_r($cwiibot_messages);
		print_r($cwiibot_ccrmessages);
	}
	if($cmd == 'reload') {
		$cwiibot_messages = cwiibot_m_getMessages();
		$cwiibot_ccrmessages = cwiibot_m_getCCRMessages();
		return;
	}
	if($cmd == 'ccr') {
		if($arg != "") { $CBot->irc->say($target,$arg.": ".str_replace('\\', '', $cwiibot_ccrmessages[rand(0,(count($cwiibot_ccrmessages)-1))])); }
		else { $CBot->irc->say($target,str_replace('\\', '', $cwiibot_ccrmessages[rand(0,(count($cwiibot_ccrmessages)-1))])); }
		return;
	}
	if (array_key_exists($cmd, $cwiibot_messages)) {
		if(substr($cwiibot_messages[$cmd],0,1) == '.')
		{
			$cmd = substr($cwiibot_messages[$cmd],1);
		}
		$messages[$cmd] = str_replace('\\', '', $cwiibot_messages[$cmd]);
		if($arg != "") { $CBot->irc->say($target,$arg.": ".$cwiibot_messages[$cmd]); }
		else { $CBot->irc->say($target,$cwiibot_messages[$cmd]); }
		return;
	}
}