<?php

class base_forum {
	
	// Core class
	public $core;
	
	// Names of common pages
	public $indexpage;
	public $forumdisplay;
	public $showthread;
	public $showpost;
	public $memberprofile;
	public $usercp;
	public $newthread;
	public $newreply;
	public $loginpage;
	public $logoutpage;
	
	function __construct(&$core) {
		$this->core = $core;
	}
	
	function post($threadid, $post, $postitle, $posticon) {
		throw new Exception('Not implemented');
	}
	
	function postReply($postid, $threadid, $post, $postsubject, $posticon) {
		throw new Exception('Not implemented');
	}
	
	function newThread($forum, $threadname, $threadicon, $threadpost) {
		throw new Exception('Not implemented');
	}
	
	function setAvatar_url($url) {
		throw new Exception('Not implemented');
	}
	
	function setSignature($signature) {
		throw new Exception('Not implemented');
	}
	
	function register($username, $password, $email) {
		throw new Exception('Not implemented');
	}
	
	function login($username, $password) {
		throw new Exception('Not implemented');
	}

	function logout() {
		throw new Exception('Not implemented');
	}
}
