<?php

class System extends Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('tab');
		$this->load->vars('home_link', TRUE);
	}
	
	function index()
	{
		$tabs = array(
			array(
				'selected' => true,
				'img' => 'img/system.png',
				'link' => site_url('System'),
				'name' => 'System'
			)
		);
		
		$vars = array(
			'tabs' => $tabs,
			'title' => 'System Pages',
			'page_title' => 'System Pages'
		);
		$this->load->vars($vars);
		$this->load->view('sw/system/index');
	}
	
	function page_list()
	{
		$this->load->model('Page_model', 'page');
		$pages = $this->page->get_pages();
		
		$tabs = array(
			array(
				'selected' => false,
				'img' => 'img/system.png',
				'link' => site_url('System'),
				'name' => 'System'
			),
			array(
				'selected' => true,
				'img' => 'img/info.png',
				'link' => site_url('System/Page_List'),
				'name' => 'Page List'
			)
		);
		
		$vars = array(
			'tabs' => $tabs,
			'title' => 'Page List',
			'page_title' => 'Page list',
			'pages' => $pages
		);
		$this->load->vars($vars);
		$this->load->view('sw/system/page_list');
	}
	
	function user_list()
	{
		$users = $this->ion_auth_model->get_users();
		
		$tabs = array(
			array(
				'selected' => false,
				'img' => 'img/system.png',
				'link' => site_url('System'),
				'name' => 'System'
			),
			array(
				'selected' => true,
				'img' => 'img/info.png',
				'link' => site_url('System/User_List'),
				'name' => 'User List'
			)
		);
		
		$vars = array(
			'tabs' => $tabs,
			'title' => 'User List',
			'page_title' => 'User list',
			'users' => $users
		);
		$this->load->vars($vars);
		$this->load->view('sw/system/user_list');
	}
	
	function ajax_toolbox_update()
	{
		if(!$this->ion_auth->logged_in() || $this->input->post('show') === FALSE)
		{
			echo 'failed';
			return;
		}
		
		$this->session->set_userdata('show_toolbox', $this->input->post('show'));
		$this->ion_auth_model->update_user(array('show_toolbox' => $this->input->post('show')));
	}
}