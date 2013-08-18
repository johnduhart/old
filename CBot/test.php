<?php

require_once('core.php');

function post_connect() {
	global $MyBot;
	$MyBot->irc->join('#johnbot');
}


$MyBot = new CBot();
$MyBot->trigger = '.';
$MyBot->owner = 'CWii';
$MyBot->auth->setOwner('CWii');
$MyBot->auth->setOwnerHost('home.compwhizii.net');
$MyBot->hooks->load('core');
$MyBot->hooks->load('simple');
$MyBot->hooks->load('cwiibot');
$MyBot->hooks->addHook('motd', 'post_connect');
$MyBot->connect('irc.cluenet.org', '6667', 'CWiiBot', 'CBot v1', 'cbot');
$MyBot->coreLoop();