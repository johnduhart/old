<?php

class Page_model extends Model {
	
	var $title = '';
	var $id = 0;
	var $views = 0;
	var $locked = 0;
	var $rev_id = 0;
	var $rev_timestamp = 0;
	var $rev_ipaddress = '';
	var $rev_username = '';
	var $rev_comment = '';
	var $text_id = 0;
	var $text = '';
	var $prev_rev_id = 0;
	var $next_rev_id = 0;
	
	function load_query()
	{
		$this->db->select('page.*, page_text.*, page_revision.pagerev_id, page_revision.pagerev_timestamp, page_revision.pagerev_userip, users.username');
		$this->db->from('page AS page');
		$this->db->join('page_revision AS page_revision', 'page.page_latest = page_revision.pagerev_id', 'left');
		$this->db->join('page_text AS page_text', 'page_revision.pagerev_text = page_text.pagetext_id', 'left');
		$this->db->join('users AS users', 'page_revision.pagerev_userid = users.id', 'left');
		$this->db->limit(1);
	}
	
	/**
	 * Loads the page by title from the database
	 * 
	 * @access public
	 * @param str $page_title
	 * @return query
	 */
	function load_page($page_title)
	{
		$page_title = $this->db->escape_str($page_title);
		$this->load_query();
		$this->db->where('page_title', $page_title);
		$query = $this->db->get();
		return $this->load($query, $page_title);
	}
	
	function load_id($page_id)
	{
		$this->load_query();
		$this->db->where('page_id', $page_id);
		$query = $this->db->get();
		return $this->load($query);
	}
	
	function load_rev($rev_id)
	{
		if($this->id == 0)
			return false;
		
		$this->db->select('page_text.*, page_revision.pagerev_id, page_revision.pagerev_timestamp, page_revision.pagerev_userip, page_revision.pagerev_comment, users.username');
		$this->db->from('page_revision AS page_revision');
		$this->db->join('page_text AS page_text', 'page_revision.pagerev_text = page_text.pagetext_id', 'left');
		$this->db->join('users AS users', 'page_revision.pagerev_userid = users.id', 'left');
		$this->db->where('pagerev_id', $rev_id);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() == 0)
			return false;
		
		$row = $query->row();
		$this->rev_id	= $row->pagerev_id;
		$this->rev_timestamp = $row->pagerev_timestamp;
		$this->rev_username = $row->username;
		$this->rev_ipaddress = $row->pagerev_userip;
		$this->rev_comment = $row->pagerev_comment;
		$this->text_id	= $row->pagetext_id;
		$this->text		= $row->pagetext_text;
		return $query;
	}
	
	function load_rev_surrounding($rev_id)
	{
		if($this->id == 0)
			return false;
		
		$this->db->select('page_revision.pagerev_id');
		$this->db->from('page_revision AS page_revision');
		$this->db->where('pagerev_id < '.$rev_id.' AND pagerev_page = '.$this->id);
		$this->db->order_by('pagerev_timestamp', 'desc');
		$this->db->limit(1);
		$prev_query = $this->db->get();
		if($prev_query->num_rows() > 0)
			$this->prev_rev_id = $prev_query->row()->pagerev_id;
		
		$this->db->select('page_revision.pagerev_id');
		$this->db->from('page_revision AS page_revision');
		$this->db->where('pagerev_id > '.$rev_id.' AND pagerev_page = '.$this->id);
		$this->db->order_by('pagerev_timestamp', 'asc');
		$this->db->limit(1);
		$next_query = $this->db->get();
		if($next_query->num_rows() > 0)
			$this->next_rev_id = $next_query->row()->pagerev_id;
	}
	
	function load(&$query, $title = '')
	{
		if( $query->num_rows() == 0 )
		{
			return false;
		}
		
		$row = $query->row();
		$this->title	= $row->page_title;
		$this->id		= $row->page_id;
		$this->views	= $row->page_views;
		$this->locked	= $row->page_locked;
		$this->rev_id	= $row->pagerev_id;
		$this->rev_timestamp = $row->pagerev_timestamp;
		$this->rev_username = $row->username;
		$this->rev_ipaddress = $row->pagerev_userip;
		$this->text_id	= $row->pagetext_id;
		$this->text		= $row->pagetext_text;
		
		return $query;
	}
	
	function edit($text, $comment = '', $create = false)
	{
		$page_text = array(
			'pagetext_text' => $text
		);
		$this->db->insert('page_text', $page_text);
		$this->text_id = $this->db->insert_id();
		
		$page_revision = array(
			'pagerev_page' => $this->id,
			'pagerev_text' => $this->text_id,
			'pagerev_comment' => $comment,
			'pagerev_userid' => (($this->session->userdata('user_id')) ? $this->session->userdata('user_id') : '0' ),
			'pagerev_userip' => $this->input->ip_address(),
			'pagerev_timestamp' => time(),
			'pagerev_type' => ($create) ? 'create' : 'edit'
		);
		$this->db->insert('page_revision', $page_revision);
		$this->rev_id = $this->db->insert_id();
		
		$page = array(
			'page_latest' => $this->rev_id
		);
		$this->db->where('page_id', $this->id);
		$this->db->update('page', $page);
	}
	
	function create($title, $text, $comment = '')
	{
		$page = array(
			'page_title' => $title,
		);
		$this->db->insert('page', $page);
		$this->id = $this->db->insert_id();
		$this->edit($text, $comment, true);
	}
	
	function update_protection($level)
	{
		$page = array(
			'page_locked' => $level
		);
		$this->db->where('page_id', $this->id);
		$this->db->update('page', $page);
		
		$page_revision = array(
			'pagerev_page' => $this->id,
			'pagerev_text' => $this->text_id,
			'pagerev_comment' => 'Protection level changed (Level '.$this->locked.' -> '.$level.' )',
			'pagerev_userid' => (($this->session->userdata('user_id')) ? $this->session->userdata('user_id') : '0' ),
			'pagerev_userip' => $this->input->ip_address(),
			'pagerev_timestamp' => time(),
			'pagerev_type' => 'protection'
		);
		$this->db->insert('page_revision', $page_revision);
		$this->rev_id = $this->db->insert_id();
	}
	
	function delete($reason = '')
	{
		$page_deleted = array(
			'pagedel_oldid' => $this->id,
			'pagedel_title' => $this->title,
			'pagedel_views' => $this->views,
			'pagedel_latest' => $this->rev_id,
			'pagedel_locked' => $this->locked
		);
		$this->db->insert('page_deleted', $page_deleted);
		
		$this->db->where('page_id', $this->id);
		$this->db->delete('page');
		
		$page_revision = array(
			'pagerev_page' => $this->id,
			'pagerev_text' => $this->text_id,
			'pagerev_comment' => $reason,
			'pagerev_userid' => (($this->session->userdata('user_id')) ? $this->session->userdata('user_id') : '0' ),
			'pagerev_userip' => $this->input->ip_address(),
			'pagerev_timestamp' => time(),
			'pagerev_type' => 'delete'
		);
		$this->db->insert('page_revision', $page_revision);
		
		$page_revision2 = array(
			'pagerev_deleted' => '1'
		);
		$this->db->where('pagerev_page', $this->id);
		$this->db->update('page_revision', $page_revision2);
	}
	
	function undelete($deleted_id, $reason = '')
	{
		$this->db->from('page_deleted AS page_deleted');
		$this->db->limit(1);
		$this->db->where('pagedel_id', $deleted_id);
		$query = $this->db->get();
		
		$row = $query->row();
		$this->title	= $row->pagedel_title;
		$this->rev_id	= $row->pagedel_latest;
		$this->views	= $row->pagedel_views;
		$this->locked	= $row->pagedel_locked;
		
		$page = array(
			'page_title'	=> $this->title,
			'page_views'	=> $this->views,
			'page_latest'	=> $this->latest,
			'page_locked'	=> $this->locked
		);
		$this->db->insert('page', $page);
		$newid = $this->db->insert_id();
		
		$page_revision = array(
			'pagerev_deleted' => '0',
			'pagerev_page' => $newid
		);
		$this->db->where('pagerev_page', $this->id);
		$this->db->update('page_revision', $page_revision);
		
		$this->id = $newid;
		
		$page_revision2 = array(
			'pagerev_page' => $this->id,
			'pagerev_text' => $this->text_id,
			'pagerev_comment' => $reason,
			'pagerev_userid' => (($this->session->userdata('user_id')) ? $this->session->userdata('user_id') : '0' ),
			'pagerev_userip' => $this->input->ip_address(),
			'pagerev_timestamp' => time(),
			'pagerev_type' => 'undelete'
		);
		$this->db->insert('page_revision', $page_revision2);
	}
	
	function previous_deleted($title = '')
	{
		if($title == '')
		{
			if($this->id != 0)
			{
				$title = $this->title;
			}
			else
			{
				return false;
			}
		}
		
		$this->db->from('page_deleted AS page_deleted');
		$this->db->where('pagedel_title', $title);
		
		return $this->db->get();
	}
	
	function get_pages($limit = 50, $start = 0)
	{
		$this->db->from('page');
		$this->db->select('page_title');
		$this->db->order_by('page_title', 'asc');
		$this->db->limit($limit, $start);
		return $this->db->get();
	}
}