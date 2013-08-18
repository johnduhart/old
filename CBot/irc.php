<?php

class irc {
	
	private $socket;
	private $eerno;
	private $errstr;
	public $run;
	
	function connect($server, $port) {
		$this->socket = stream_socket_client('tcp://'.$server.':'.$port,$this->errno,$this->errstr,30);
	}
	
	function ircLine() {
		global $CBot;
		$line = str_replace(array("\n","\r"),'',fgets($this->socket,512));
		if(str_replace(' ','',$line) == '') {
			return false;
		}
		$tmp = explode(' ',$line);
		$cmd = $tmp[0];
		if (strtolower($cmd) == 'ping') {
			$this->raw('PONG '.$tmp[1]);
		} else {
			//$CBot->handleLine($line);
			return $line;
		}
	}
	
	function raw($str) {
		fwrite($this->socket, $str."\n");
	}
	
	function nick($nick) {
		global $CBot;
		$this->raw('NICK '.$nick);
		$CBot->nick = $nick;
	}
	
	function say($target, $msg) {
		$this->raw('PRIVMSG '.$target.' :'.$msg);
	}
	
	function notice($target, $msg) {
		$this->raw('NOTICE '.$target.' :'.$msg);
	}
	
	function action($target, $msg) {
		$this->raw('PRIVMSG '.$target.' :'."\x01ACTION ".$msg."\x01");
	}
	
	function join($chan, $key='') {
		$this->raw('JOIN '.$chan.' '.$key);
	}
	
	function part($chan, $msg='') {
		$this->raw('PART '.$chan.' :'.$msg);
	}
	
	function invite($chan, $nick) {
		$this->raw('INVITE '.$nick.' '.$chan);
	}
	
	function topic($chan, $topic) {
		$this->raw('TOPIC '.$chan.' :'.$topic);
	}
	
	function kick($chan, $nick, $msg) {
		$this->raw('KICK '.$chan.' '.$nick.' :'.$msg);
	}
	
	function mode($chan, $mode) {
		$this->raw('MODE '.$chan.' '.$mode);
	}
	function quit($msg) {
		$this->raw('QUIT :'.$msg);
	}
}
