<?php

class mybb_forum extends base_forum {
	
	// Names of common pages
	public $indexpage;
	public $forumdisplay;
	public $showthread;
	public $showpost;
	public $memberprofile;
	public $usercp = "usercp.php";
	public $newthread = "newthread.php";
	public $newreply = "newreply.php";
	public $loginpage = "member.php";
	public $logoutpage = "member.php";
	
	private $postkey = "";
	
	function __construct(&$base) {
		parent::__construct($base);
	}

	function post($threadid, $post, $postsubject, $posticon) {
		$key = $this->getPostKey($this->core->baseUrl.$this->newreply.'?tid='.$threadid);
		$data = array(
			'my_post_key' => $key,
			'subject' => $postsubject,
			'icon' => $posticon,
			'message' => $post,
			'action' => 'do_newreply'
		);
		$page = $this->core->baseUrl.$this->newreply."?tid=".$threadid."&processed=1";
		$return = $this->core->cUrl->post($page, $data);
	}
	
	function postReply($postid, $threadid, $post, $postsubject, $posticon) {
		$postpage = $this->core->cUrl->get($this->core->baseUrl.$this->newreply.'?tid='.$threadid.'&pid='.$postid);
		if (preg_match('%<textarea id="message" name="message".*?>((.|\s)*?)</textarea>%', $postpage, $regs)) {
			$quote = $regs[1];
		} else {
			echo "No quote";
			return;
		}
		$key = $this->getPostKey($this->core->baseUrl.$this->newreply.'?tid='.$threadid);
		
		$data = array(
			'my_post_key' => $key,
			'subject' => $postsubject,
			'icon' => $posticon,
			'message' => $quote.$post,
			'action' => 'do_newreply'
		);
		$page = $this->core->baseUrl.$this->newreply.'?tid='.$threadid.'&processed=1';
		$return = $this->core->cUrl->post($page, $data);
	}
	
	function newThread($forumid, $threadname, $threadicon, $threadpost) {
		$key = $this->getPostKey($this->core->baseUrl.$this->newthread."?fid=".$forumid);
		
		$data = array(
			'my_post_key' => $key,
			'subject' => $threadname,
			'icon' => $threadicon,
			'message' => $threadpost,
			'action' => 'do_newthread'
		);
		$page = $this->core->baseUrl.$this->newthread."?fid=".$forumid."&processed=1";
		
		$return = $this->core->cUrl->post($page, $data);
	}
	
	function setAvatar_url($url) {
		$key = $this->getPostKey($this->core->baseUrl.$this->usercp."?action=avatar");
		
		$data = array(
			'my_post_key' => $key,
			'action' => 'do_avatar',
			'avatarurl' => $url
		);
		$page = $this->core->baseUrl.$this->usercp;
		$return = $this->core->cUrl->post($page, $data);
	}
	
	function setSignature($signature) {
		$key = $this->getPostKey($this->core->baseUrl.$this->usercp."?action=editsig");
		
		$data = array(
			'my_post_key' => $key,
			'action' => 'do_editsig',
			'signature' => $signature,
			'updateposts' => 'enable'
		);
		$page = $this->core->baseUrl.$this->usercp;
		$return = $this->core->cUrl->post($page, $data);
	}
	
	function register($username, $password, $email) {
		throw new Exception('Not implemented');
	}
	
	function login($username, $password) {
		$data = array(
			'username' => $username,
			'password' => $password,
			'action' => 'do_login'
		);
		
		$page = $this->core->baseUrl.$this->loginpage;
		$return = $this->core->cUrl->post($page, $data);
	}
	
	function logout() {
		$page = $this->core->baseUrl.$this->logoutpage.'?action=logout';
		$this->core->cUrl->get($page);
		$this->postkey = "";
	}
	
	function getPostKey($page) {
		if ($this->postkey == "") {
			$keypage = $this->core->cUrl->get($page);
			if (preg_match('%<input type="hidden" name="my_post_key" value="(.*)" />%', $keypage, $regs)) {
				$key = $regs[1];
			} else {
				$key = "";
			}
			$this->postkey = $key;
			return $key;
		} else {
			return $this->postkey;
		}
	}
}
