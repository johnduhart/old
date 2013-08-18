<?php

class Page extends Controller {
	
	function __construct()
	{	
		parent::__construct();
		
		$this->load->helper('tab');
		$this->load->model('Page_model', 'page');
	}
	
	function index()
	{
		// Return the index page
		$this->view('Main Page');
	}
	
	function view($page, $rev = 0)
	{
		$this->load->library('parser');
		$page = urldecode($page);
		$page_title = str_replace(array(' '), array('_'), $page);
		$page = str_replace(array('_'), array(' '), $page);
		
		$query = $this->page->load_page($page_title);
		
		$show = array();
		$show['page_rev'] = false;
		
		if($query === FALSE)
		{
			// The page doesn't exist
			$this->page->text = 'This page doesn\'t exist yet, press edit to create it';
		}
		elseif($rev != 0)
		{
			$query_rev = $this->page->load_rev($rev);
			if($query_rev === FALSE)
			{
				$this->page->text = 'Bad revision ID';
			}
			else
			{
				$show['page_rev'] = true;
				$this->page->load_rev_surrounding($rev);
				$rev_vars = array(
					'next_rev' => $this->page->next_rev_id,
					'prev_rev' => $this->page->prev_rev_id
				);
				$this->load->vars($rev_vars);
			}
		}
		
		$aText = $this->parser->parse($this->page->text);
		
		$tabs = array(
			array(
				'selected' => true,
				'img' => 'img/view.png',
				'link' => site_url($page_title),
				'name' => 'Read'
			),
			array(
				'selected' => false,
				'img' => 'img/edit.png',
				'link' => site_url($page_title.'/edit'),
				'name' => 'Edit'
			)
		);
		
		$vars = array(
			'tabs' => $tabs,
			'title' => $page,
			'page_title' => $page,
			'text' => $aText,
			'show' => $show,
			'timestamp' => $this->page->rev_timestamp,
			'user' => (($this->page->rev_username != '') ? $this->page->rev_username : $this->page->rev_ipaddress),
			'comment' => $this->page->rev_comment
		);
		$this->load->vars($vars);
		$this->load->view('sw/page/view');
		
		$this->session->set_userdata('last_page', current_url());
	}
	
	function edit($page, $section = '')
	{
		// Check to see if we're editing the page and doing a preview
		if($this->input->post('pageid') !== FALSE && $this->input->post('preview') !== FALSE)
		{
			// Load the parser and generate the page
			$this->load->library('parser');
			$this->load->vars('preview_text', $this->parser->parse($this->input->post('editbox')));
			
			$show_preview = true;
		}
		elseif($this->input->post('pageid') !== FALSE)
		{
			if($this->input->post('pageid') != 0)
			{
				$this->page->load_id($this->input->post('pageid'));
				
				// Check to see if we have permission to edit this page
				if(($this->page->locked == 2 && $this->ion_auth->is_admin())
					OR
					($this->page->locked == 1 && $this->ion_auth->logged_in()))
				{
					$this->page->edit($this->input->post('editbox'), $this->input->post('comment'));
					redirect($this->_make_link($page));
					return;
				}
			}
			else
			{
				// New page
				
				$comment = ($this->input->post('comment') == '') ? 'Page created' : $this->input->post('comment');
				$this->page->create($this->_make_link($page), $this->input->post('editbox'),
									 $comment);
				redirect($this->_make_link($page));
				return;
			}
			
			// Something has fallen through, so we need to display the edit page...
		}
		
		$this->load->helper('form');
		$page = urldecode($page);
		$page_title = $this->_make_link($page);
		$page = str_replace(array('_'), array(' '), $page);
		
		$this->page->load_page($page_title);
		
		$tabs = array(
			array(
				'selected' => false,
				'img' => 'img/view.png',
				'link' => site_url($page_title),
				'name' => 'Read'
			),
			array(
				'selected' => true,
				'img' => 'img/edit.png',
				'link' => site_url($page_title.'/edit'),
				'name' => 'Edit'
			)
		);
		
		$show = array(
			'newpage_notice' => ($this->page->id == 0),
			// We need to add a permissions error
			'save_buttons' => (
									($this->page->id == 0 && $this->ion_auth->logged_in())
								OR 	
									(
										($this->page->locked == 0)
									OR
										($this->ion_auth->logged_in() && $this->page->locked == 1)
									OR
										($this->ion_auth->is_admin())
									)
								),
			'tools' => ($this->page->id != 0),
			'mod_tools' => ($this->ion_auth->is_admin()),
			'report' => ($this->ion_auth->logged_in()),
			'previous_deleted' => ($this->page->id == 0 && $this->ion_auth->is_admin() && $this->page->previous_deleted($page_title)->num_rows() > 0),
			'edit_preview' => isset($show_preview)
		);
		
		$vars = array(
			'tabs' => $tabs,
			'title' => $page,
			'page_title' => $page,		// This is confusing
			'page_link' => $page_title, // :p
			'headinclude' => $this->load->view('sw/page/edit_headinclude', '', TRUE),
			'bottom_script' => $this->load->view('sw/page/edit_bottom_script', '', TRUE),
			'editText' => ($this->input->post('editbox') === FALSE) ? $this->page->text : $this->input->post('editbox'),
			'comment' => ($this->input->post('comment') === FALSE) ? '' : $this->input->post('comment'),
			'locked_status' => $this->page->locked,
			'pageid' => $this->page->id,
			'protection_link' => site_url('ajax/page/update_protection'),
			'delete_link' => site_url('ajax/page/delete'),
			'show' => $show
		);
		$this->load->vars($vars);
		$this->load->view('sw/page/edit');
		
		$this->session->set_userdata('last_page', current_url());
	}
	
	function history($page)
	{
		$this->load->model('Revision_model', 'revision');
		$page = urldecode($page);
		$page_title = $this->_make_link($page);
		$page = str_replace(array('_'), array(' '), $page);
		
		$this->page->load_page($page_title);
		if($this->page->id == 0)
		{
			die('Bad page');
		}
		$revs = $this->revision->get_latest_revisions($this->page->id, 20);
		
		$tabs = array(
			array(
				'selected' => false,
				'img' => 'img/view.png',
				'link' => site_url($page_title),
				'name' => 'Read'
			),
			array(
				'selected' => true,
				'img' => 'img/edit.png',
				'link' => site_url($page_title.'/edit'),
				'name' => 'Edit'
			)
		);
		
		$vars = array(
			'tabs' => $tabs,
			'title' => $page,
			'page_title' => $page,		// This is confusing
			'page_link' => $page_title, // :p
			'revs' => $revs
		);
		$this->load->vars($vars);
		$this->load->view('sw/page/history');
		
		$this->session->set_userdata('last_page', current_url());
	}
	
	function ajax_update_protection()
	{
		if(!$this->ion_auth->is_admin())
		{
			return;
		}
		if(!$this->input->post('pageid') || $this->input->post('newlevel') === FALSE)
		{
			print json_encode(array('error' => 'Missing pageid/newlevel'));
			return;
		}
		$this->page->load_id($this->input->post('pageid'));
		$this->page->update_protection($this->input->post('newlevel'));
		print json_encode(array('success' => 'Protection level changed'));
	}
	
	function ajax_delete()
	{
		if(!$this->ion_auth->is_admin())
		{
			return;
		}
		if(!$this->input->post('pageid') || $this->input->post('reason') === FALSE)
		{
			print json_encode(array('error' => 'Missing pageid/reason'));
			return;
		}
		$this->page->load_id($this->input->post('pageid'));
		$this->page->delete($this->input->post('reason'));
		print json_encode(array('success' => 'Page deleted'));
	}
	
	function _make_link($page)
	{
		return str_replace(array(' '), array('_'), $page);
	}
}