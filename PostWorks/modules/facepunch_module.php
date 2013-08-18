<?php

class facepunch_module extends base_module {
	
	public $ratingspage = "fp_ratings.php";
	
	function __construct(&$core) {
		parent::__construct($core);
	}
	
	function ratePost($postid, $rating) {
		$page = $this->core->baseUrl.$this->core->forum->showpost.'?p='.$postid.'&postcount=1';
		$post = $this->core->cUrl->get($page);
		
		if (preg_match('%{ postid: \'$postid\', rating: \'$rating\', key: \'(.*?)\' }%', $post, $regs)) {
			$key = $regs[1];
		} else {
			echo "Couldn't find rating key!";
			return;
		}
		$page = $this->core->baseUrl.$this->ratingspage;
		$data = array(
			'postid' => $postid,
			'rating' => $rating,
			'key' => $key
		);
		$ratepost = $this->core->cUrl->post($page, $data);
	}
}
