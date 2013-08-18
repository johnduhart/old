<?php

class Auth extends Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper(array('tab', 'form'));
		$this->load->vars('home_link', TRUE);
	}
	
	function index()
	{
		
	}
	
	function login()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_error_delimiters('', '<br />');
		
		if($this->form_validation->run() == true)
		{
			// We're logging in
			$remember = (bool) $this->input->post('remember');
			
			if($this->ion_auth->login($this->input->post('username'), $this->input->post('password'), $remember))
			{
				$url = ($this->session->userdata('last_page') !== FALSE) ? $this->session->userdata('last_page') : base_url();
				
				// We're logged in!
				$vars = array(
						'title' => 'Success!',
						's_title' => 'You have successfully logged in',
						's_desc' => 'You will now be redirected back where you were, or <a href="'.$url.'">skip waiting</a>',
						'redirect' => array('length' => '2', 'url' => $url)
					);
				$this->load->vars($vars);
				$this->load->view('sw/redirect');
			}
			else
			{
				// We failed :(
				$this->session->set_flashdata('message', "Login Failed");
				redirect('auth/login', 'refresh');
			}
		}
		else
		{
			// We're displaying the login page
			
			$error = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			
			$tabs = array(
				array(
					'selected' => true,
					'img' => 'img/login.png',
					'link' => site_url('auth/login'),
					'name' => 'Login'
				),
				array(
					'selected' => false,
					'img' => 'img/register.png',
					'link' => site_url('auth/register'),
					'name' => 'Register'
				)
			);
			
			$vars = array(
				'tabs' => $tabs,
				'title' => 'Login',
				'error' => $error
			);
			$this->load->vars($vars);
			
			$this->load->view('sw/auth/login');
		}
	}
	
	function register()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|trim|callback__username_check');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback__email_check');
		$this->form_validation->set_error_delimiters('', '<br />');
		
		if($this->form_validation->run() == true)
		{
			$this->ion_auth->register($this->input->post('username'),
				$this->input->post('password'), $this->input->post('email'), array());
			$this->ion_auth->login($this->input->post('username'), $this->input->post('password'), true);
			$url = ($this->session->userdata('last_page') !== FALSE) ? $this->session->userdata('last_page') : base_url();
			$vars = array(
					'title' => 'Success!',
					's_title' => 'Your account has been created',
					's_desc' => '<a href="'.$url.'">Go back to where you were</a>'
				);
			$this->load->vars($vars);
			$this->load->view('sw/redirect');
		}
		else
		{
			$error = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			
			$tabs = array(
				array(
					'selected' => false,
					'img' => 'img/login.png',
					'link' => site_url('auth/login'),
					'name' => 'Login'
				),
				array(
					'selected' => true,
					'img' => 'img/register.png',
					'link' => site_url('auth/register'),
					'name' => 'Register'
				)
			);
			
			$vars = array(
				'tabs' => $tabs,
				'title' => 'Register',
				'error' => $error
			);
			$this->load->vars($vars);
			
			$this->load->view('sw/auth/register');
		}
	} 
	
	function logout()
	{
		$this->ion_auth->logout();
		
		$url = ($this->session->userdata('last_page') !== FALSE) ? $this->session->userdata('last_page') : base_url();
		
		$vars = array(
				'title' => 'Success!',
				's_title' => 'You have been logged out',
				's_desc' => 'You will be redirected back where you were, or <a href="'.$url.'">skip waiting</a>',
				'redirect' => array('length' => '2', 'url' => $url)
			);
		$this->load->vars($vars);
		$this->load->view('sw/redirect');
	}
	
	function _username_check($str)
	{
		// Sadly, while Ion does check to see if a username is taken, it
		// does it in a crude fashion, not returning an email and just
		// incrementing a number at the end of the username.
		// Gross.  --John
		$this->load->model('ion_auth_model');
		if($this->ion_auth_model->username_check($this->input->post('username')))
		{
			// NO DUPES FOR YOU!
			$this->form_validation->set_message('_username_check', "Username taken");
			return false;
		}
		return true;
	}
	
	function _email_check($str)
	{
		$this->load->model('ion_auth_model');
		if($this->ion_auth_model->email_check($this->input->post('email')))
		{
			$this->form_validation->set_message('_email_check', "That email address is in use");
			return false;
		}
		return true;
	}
}