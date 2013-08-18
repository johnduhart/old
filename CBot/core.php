<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

require_once('irc.php');
require_once('hooks.php');
require_once('scheduler.php');
require_once('auth.php');

class CBot {
	
	public $irc;
	public $hooks;
	public $db;
	public $scheduler;
	public $auth;
	public $trigger;
	public $run;
	public $owner;
	public $debug_message;
	public $ignored;
	public $nick;
	
	function __construct() {
		$GLOBALS['CBot'] =& $this;
		
		$this->irc = new irc();
		$this->hooks = new hookSystem();
		$this->scheduler = new scheduler();
		$this->auth = new authSystem();
		$this->debug_message = false;
		$this->ignored = array();
	}
	
	function connect($server, $port, $nick, $name, $user) {
		$this->irc->run = true;
		$this->irc->connect($server, $port);
		$this->irc->raw('USER '.$user.' "1" "1" :'.$name);
		$this->irc->nick($nick);
	}
	
	function useDB($db) {
		require_once('db/'.$db.'.php');
		eval("\$this->db = new db_".$db."();");
	}
	
	function loadLib($libName)
	{
		if(eval("return isset(\$this->".strtolower($libName).");"))
		{
			print 'ERROR! Attemped to load an already loaded library ('.$libName.")\n";
		}
		require_once('lib/'.strtolower($libName).'/'.strtolower($libName).'.php');
		eval("\$this->".strtolower($libName)." = new ".$libName."_lib();");
	}
	
	function coreLoop() {
		//Add an impossible to reach Event to prevent errors, which causes an error anyway
		//$this->scheduler->addEvent('die', time() * 2);
		$this->run = true;
		$nextStack = 0;
		while($this->run) {
			$line = $this->irc->ircline();
			if($nextStack <= time()) {
				$this->scheduler->checkEvents();
				$nextStack = time() + 5;
			}
			if ($line !== FALSE) {
				$this->handleLine($line);
			}
		}
		echo 'Leaving coreLoop';
	}
	
	function handleLine($line) {
		$this->hooks->runHook('line', array($line));
		$tmp = explode(' ',$line);
		$cmd = $tmp[0];
		if(count($tmp) < 2) {
			return;
		}
		switch (strtolower($tmp[1])) {
			case '433':
				$this->irc->nick($this->nick.'_');
				break;
			case '422':
			case '376':
				$this->hooks->runHook('motd');
				break;
			case 'privmsg':
				$nick = explode('!',substr($tmp[0],1));
				$nick = $nick[0];
				$target = $tmp[2];
				$message = explode(' ',$line,4);
				$message = substr($message[3],1);
				$host = explode('!',substr($tmp[0],1));
				$host = explode('@',$host[1]);
				$host = explode(' ',$host[1]);
				$host = $host[0];
				if(in_array(strtolower($nick), $this->ignored))
				{
					echo 'Ignored '.$nick."\n";
					break;
				}
				$this->hooks->runHook('privmsg', array($nick, $target, $message, $host));
				$this->hooks->runHook('privmsg_line', array($line));
				
				if (substr($target,0,1) != '#') {
					// This is a PM
					$this->hooks->runHook('privmsg_pm', array($nick, $target, $message, $host));
					$tmp0 = explode(' ',$message, 2);
					$cmd = strtolower($tmp0[0]);
					$arg = $tmp0[1];
					$this->hooks->runHook('privmsg_pmcmds', array($nick, $target, $message, $host, $cmd, $arg));
				} else {
					$this->hooks->runHook('privmsg_chan', array($nick, $target, $message, $host));
					if (substr($message,0,1) == $this->trigger) {
						$tmp0 = explode(' ',$message, 2);
						$cmd = strtolower(substr($tmp0[0],1));
						$arg = $tmp0[1];
						$this->hooks->runHook('privmsg_chancmds', array($nick, $target, $message, $host, $cmd, $arg));
					}
				}
				break;
			case 'join':
				$nick = explode('!',substr($tmp[0],1));
				$nick = $nick[0];
				$target = substr($tmp[2], 1);
				$host = explode('!',substr($tmp[0],1));
				$host = explode('@',$host[1]);
				$host = explode(' ',$host[1]);
				$host = $host[0];
				
				debug('Nick: '.$nick);
				debug('Target: '.$target);
				debug('Host: '.$host);
				
				if(in_array(strtolower($nick), $this->ignored))
				{
					echo 'Ignored '.$nick."\n";
					break;
				}
				$this->hooks->runHook('join', array($nick, $target, $host));
				$this->hooks->runHook('join_line', array($line));
				break;
		}
	}
}

// Helper functions

function debug($msg)
{
		global $CBot;
		
		if($CBot->debug_message)
		{
			print 'D: '.$msg."\n";
		}
}