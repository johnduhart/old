<?php

class User extends Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('tab');
		$this->load->vars('home_link', TRUE);
	}
	
	function index()
	{
		die('This shouldn\'t happen');
	}
	
	function view($username)
	{
		$user = $this->ion_auth_model->get_user_by_username($username);
		if($user->num_rows() == 0)
		{
			echo 'Invaild user';
			return;
		}
		$user = $user->row();
		
		$tabs = array(
			array(
				'selected' => true,
				'img' => 'img/user.png',
				'link' => site_url('User/'.$username),
				'name' => 'User'
			),
			array(
				'selected' => false,
				'img' => 'img/message_write.png',
				'link' => site_url('User/'.$username.'/message'),
				'name' => 'Message'
			)
		);
		
		$vars = array(
			'tabs' => $tabs,
			'title' => $username,
			'page_title' => $username,
			'user' => $user,
			'top_extra' => "| <a href=\"".site_url('System/User_List')."\">User List</a>"
		);
		$this->load->vars($vars);
		$this->load->view('sw/user/view_user');
	}
	
	function message($username)
	{
		echo 'Not implemented';
	}
}