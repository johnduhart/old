<?php

class Revision_model extends Model {
	
	function get_latest_revisions($page_id, $limit = 20)
	{
		$this->db->from('page_revision AS page_revision');
		$this->db->select('page_revision.*, user.username');
		$this->db->join('users AS user', 'page_revision.pagerev_userid = user.id', 'left');
		$this->db->where('page_revision.pagerev_page', $page_id);
		$this->db->order_by('page_revision.pagerev_timestamp', 'desc');
		$this->db->limit($limit);
		
		return $this->db->get();
	}
	
}