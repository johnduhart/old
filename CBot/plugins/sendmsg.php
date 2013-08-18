<?php

require_once('lib/phpmailer/class.phpmailer.php');

/*

Please set $mailcfg

define('EMAILUSER', 'john@compwhizii.net');
define('EMAILPASS', 'pass');

$mailcfg = array(
	'host' => "smtp.mail.com",
	'port' => '123'
	'fromaddr' => 'yourbot@mail.com'
	'fromname' => 'YourBot',
	'sendto' => 'you@mail.com'
);


*/


function sendmsg_load() {
	global $CBot;
	
	$CBot->hooks->addHook('privmsg_chan', 'sendmsg_chan');
	$CBot->hooks->addHook('privmsg_chancmds', 'sendmsg_cmd');
}

function sendmsg_unload() {
	global $CBot;
	
	$CBot->hooks->removeHook('privmsg_chan', 'sendmsg_chan');
	$CBot->hooks->removeHook('privmsg_chancmds', 'sendmsg_cmd');
}

function sendmsg_chan($nick, $target, $message, $host) {
	global $CBot, $lastmsg, $lastnotify;
	
	if($nick == $CBot->owner) {
		$lastmsg = time();
	}
	
	if($lastmsg < (time() - 1800) && preg_match("/cwii/i", $message)) {
		if(!array_key_exists($nick, $lastnotify) && $lastnotify[$nick] < (time() - 600)) {
			$CBot->irc->notice($nick, 'CWii may not be here right now, but if it\'s important please send him a message with .CWii <message>');
			$lastnotify[$nick] = time();
		}
	}
}

function sendmsg_cmd($nick, $target, $message, $host, $cmd, $arg) {
	global $CBot, $mailcfg, $lastmsg;
	
	if($cmd == 'cwii') {		
		$subject = "Notication from ".$nick." on ".$target;
		$body = "Channel: ".$target."\n";
		$body.= "From: ".$nick."\n";
		$body.= "At: ".date("n-j-o h:i:s A")."\n";
		$body.= "Msg: ".$arg;
		smtpmailer($mailcfg['sendto'], $mailcfg['fromaddr'], $mailcfg['fromname'], $subject, $body, $nick);
	}
	if($nick == $CBot->owner && $cmd == 'afk') {
		$lastmsg = 0;
	}
}

function smtpmailer($to, $from, $from_name, $subject, $body, $nick) { 
	global $CBot, $mailcfg;
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465; 
	$mail->Username = EMAILUSER;  
	$mail->Password = EMAILPASS;           
	$mail->SetFrom($from, $from_name);
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->AddAddress($to);
	if(!$mail->Send()) {
		$CBot->irc->notice($nick, 'Email failed: '.$mail->ErrorInfo);
		return false;
	} else {
		$CBot->irc->notice($nick, 'Email sent');
		return true;
	}
}