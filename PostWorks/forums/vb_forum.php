<?php

class vb_forum extends base_forum {
	
	// Core class
	public $core;
	
	// Names of common pages
	public $indexpage = "index.php";
	public $forumdisplay = "forumdisplay.php";
	public $showthread = "showthread.php";
	public $showpost = "showpost.php";
	public $memberprofile;
	public $usercp = "usercp.php";
	public $profilecp = "profile.php";
	public $newthread = "newthread.php";
	public $newreply = "newreply.php";
	public $loginpage = "login.php";
	public $logoutpage = "login.php";
	
	// Time limits for actions
	public $postlimit = 30;
	public $threadlimit = 30;
	public $pmlimit = 60;
	
	private $token = "";
	private $lastpost = 0;
	private $lastthread = 0;
	private $lastpm = 0;
	
	function __construct(&$core) {
		$this->core = $core;
	}
	
	function post($threadid, $post, $posttitle, $posticon) {
		if(time() < $this->lastpost + $this->postlimit)
		{
			$sleep = ($this->lastpost + $this->postlimit) - time() + 1;
			wmsg('Rate limit for posts hit, sleeping for '.$sleep.' seconds.');
			sleep($sleep);
		}
		
		$token = $this->getToken($this->core->baseUrl.$this->newreply.'?do=newreply&t='.$threadid);
		
		$data = array(
			'title' => $posttitle,
			'message' => $post,
			'iconid' => $posticon,
			'securitytoken' => $token,
			'do' => 'postreply',
			't' => $threadid
		);
		$page = $this->core->baseUrl.$this->newreply.'?do=postreply&t='.$threadid;
		$return = $this->curl_post($page, $data);
		$this->lastpost = time();
	}
	
	function postReply($postid, $threadid, $post, $posttitle, $posticon) {
		if(time() < $this->lastpost + $this->postlimit)
		{
			$sleep = ($this->lastpost + $this->postlimit) - time() + 1;
			wmsg('Rate limit for posts hit, sleeping for '.$sleep.' seconds.');
			sleep($sleep);
		}
		
		$token = $this->getToken($this->core->baseUrl.$this->newreply.'?do=newreply&p='.$postid);
		$postpage = $this->curl_get($this->core->baseUrl.$this->newreply.'?p='.$postid);
		if (preg_match('%<textarea name="message".*?>((.|\s)*?)</textarea>%', $postpage, $regs)) {
			$quote = $regs[1];
		} else {
			echo "No quote";
			return;
		}
		
		$data = array(
			'title' => $posttitle,
			'message' => $quote.$post,
			'iconid' => $posticon,
			'securitytoken' => $token,
			'do' => 'postreply',
			'p' => $postid,
			'specifiedpost' => '1'
		);
		$page = $this->core->baseUrl.$this->newreply.'?do=postreply&t='.$threadid;
		$return = $this->curl_post($page, $data);
		$this->lastpost = time();
	}
	
	function newThread($forum, $threadname, $threadicon, $threadpost, $tags="") {
		if(time() < $this->lastthread + $this->threadlimit)
		{
			$sleep = ($this->lastthread + $this->threadlimit) - time() + 1;
			wmsg('Rate limit for threads hit, sleeping for '.$sleep.' seconds.');
			sleep($sleep);
		}
		
		$token = $this->getToken($this->core->baseUrl.$this->newthread.'?do=newthread&f='.$forum);
		
		$data = array(
			'subject' => $threadname,
			'message' => $threadpost,
			'taglist' => $tags,
			'iconid' => $threadicon,
			'securitytoken' => $token,
			'f' => $forum,
			'do' => 'postthread'
		);
		$page = $this->core->baseUrl.$this->newthread.'?do=postthread&f='.$forum;
		$return = $this->curl_post($page, $data);
	}
	
	function setAvatar_url($url) {
		$token = $this->getToken($this->core->baseUrl.$this->profilecp.'?do=editavatar');
		
		$data = array(
			'securitytoken' => $token,
			'do' => 'updateavatar',
			'avatarid' => '0',
			'avatarurl' => $url
		);
		$page = $this->core->baseUrl.$this->profilecp.'?do=editavatar';
		$return = $this->curl_post($page, $data);
	}
	
	function setSignature($signature) {
		$token = $this->getToken($this->core->baseUrl.$this->profilecp.'?do=editsignature');
		
		$data = array(
			'message' => $signature,
			'securitytoken' => $token,
			'do' => 'updatesignature',
		);
		$page = $this->core->baseUrl.$this->profilecp.'?do=editsignature';
		$return = $this->curl_post($page, $data);
	}
	
	function register($username, $password, $email) {
		throw new Exception('Not implemented');
	}
	
	function login($username, $password) {
		$token = $this->getToken($this->core->baseUrl.$this->indexpage);
		
		$data = array(
			'vb_login_username' => $username,
			'cookieuser' => '1',
			'vb_login_password' => '',
			'vb_login_md5password' => md5($password),
			'vb_login_md5password_utf' => md5($password),
			'securitytoken' => $token,
			'do' => 'login'
		);
		$page = $this->core->baseUrl.$this->loginpage.'?do=login';
		$return = $this->curl_post($page, $data);
		$this->token = "";
	}

	function logout() {
		$indexpage = $this->curl_get($this->core->baseUrl.$this->indexpage);
		if (preg_match('%<a href="login.php?do=logout&logouthash=(.*?)"(.*?|)>%', $indexpage, $regs)) {
			$key = $regs[1];
		} else {
			echo "No key for logout!";
			return;
		}
		$page = $this->core->baseUrl.$this->logoutpage.'?do=logout&logouthash='.$key;
		$this->curl_get($page);
		$this->token = "";
	}
	
	function getToken($page) {
		if ($this->token == "") {
			$keypage = $this->curl_get($page);
			if (preg_match('%<input type="hidden" name="securitytoken" value="(.*)" />%', $keypage, $regs)) {
				$key = $regs[1];
			} else {
				$key = "";
			}
			$this->token = $key;
			return $key;
		} else {
			return $this->token;
		}
	}
	
	function curl_get($page)
	{
		return $this->core->curl->get($page);
	}
	
	function curl_post($page, $data)
	{
		return $this->core->curl->post($page, $data);
	}
}
