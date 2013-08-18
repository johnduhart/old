<?php

require_once('vb_forum.php');

class facepunch_forum extends vb_forum {
	
	function getThreads($forumid, $page = '1', $order = 'desc', $stickys = true)
	{
		$forum_page = $this->curl_get($this->core->baseUrl.$this->forumdisplay.'?f='.$forumid.'&page='.$page.'&order='.$order);
		//echo $forum_page;
		$html = str_get_html($forum_page);
		$threads = array();
		foreach($html->find('table[id=threadslist]', 0)->find('tr') as $thread)
		{
			//echo $thread;
			// Check for a thead
			if($thread->find('td.thead', 0) != null)
				continue;
			
			$sticky = false;
			// Check to see if it's sticky
			if($thread->find('td.alt2Sticky', 0) != null)
			{
				if(!$stickys)
				{
					continue;
				}
				else
				{
					$sticky = true;
				}
			}
			
			$posticon = $thread->find('td img', 0)->src;
			$col2 = $thread->find('td', 1);
			
			$threadlink = $col2->find('div div a', 0);
			$title = $threadlink->innertext;
			$threadid = explode('t=', $threadlink->href);
			$threadid = $threadid[1];
			unset($threadlink);
			
			$hasimages = ($col2->find('div div a img', 0) != null);
			
			$posterspan = $col2->find('div div.threadbitdetails span', 0);
			$posterusername = $posterspan->innertext;
			$posteruserid = explode('u=', $posterspan->onclick);
			$posteruserid = explode('\'', $posteruserid[1]);
			$posteruserid = $posteruserid[0];
			unset($posterspan, $col2);
			
			$replys = $thread->find('td', 3)->find('a', 0)->innertext;
			$views = trim($thread->find('td', 4)->innertext);
			
			$threads[] = array(
				'posticon' => $posticon,
				'sticky' => $sticky,
				'title' => $title,
				'threadid' => $threadid,
				'hasimages' => $hasimages,
				'posterusername' => $posterusername,
				'posteruserid' => $posteruserid,
				'replys' => $replys,
				'views' => $views
			);
		}
		
		return $threads;
	}
	
	function getPosts($threadid, $page = 1)
	{
		$thread_page = $this->curl_get($this->core->baseUrl.$this->showthread.'?t='.$threadid.'&page='.$page);
		$html = str_get_html($thread_page);
		$posts = array();
		//echo $html->find('div[id=posts]', 0)->find('div.spacer', 0);die;
		foreach($html->find('div[id=posts]', 0)->find('div.spacer') as $post)
		{
			// Jump down into the postbit div
			$post = $post->find('div', 0);
			
			$postid = explode(' ', $post->onmouseout);
			$postid = $postid[1];
			
			$header = $post->find('div.header', 0);
			$postnum = explode('#', trim($header->find('div a', 0)->plaintext));
			$postnum = $postnum[1];
			$postdate = trim($header->plaintext);
			preg_match('/&nbsp;((.*?)Ago)/', $postdate, $matches);
			$postdate = $matches[1];
			$header->clear();
			unset($header, $matches);
			
			$userinfo = $post->find('div.userinfo', 0);
			$username = $userinfo->find('div.username a', 0);
			$userid = explode('u=', $username->href);
			$userid = $userid[1];
			$usergroup = $this->getUsergroup($username);
			$username = $username->plaintext;
			$usertitle = $userinfo->find('div.usertitle', 0)->plaintext;
			$joinposts = $userinfo->find('div', 2);
			$joinposts = explode('<br/>', trim($joinposts->innertext));
			$joindate = trim($joinposts[0]);
			$postcount = trim($joinposts[1]);
			$userinfo->clear();
			unset($userinfo, $joinposts);
			
			$messagetext = $post->find('div.messagetext', 0)->plaintext;
			$messagehtml = $post->find('div.messagetext', 0)->innertext;
			
			$posts[] = array(
				'postid' => $postid,
				'postnum' => $postnum,
				'postdate' => $postdate,
				'userid' => $userid,
				'username' => $username,
				'usergroup' => $usergroup,
				'usertitle' => $usertitle,
				'joindate' => $joindate,
				'postcount' => $postcount,
				'messagetext' => $messagetext,
				'messagehtml' => $messagehtml
			);
			
			$post->clear();
			unset($post);
		}
		$html->clear();
		unset($html, $thread_page);
		return $posts;
	}
	
	function getUsergroup(&$username)
	{
		if($username->find('strong font[color=#A06000]', 0) != null)
			return 'gold';
		
		if($username->find('font[color=#00aa00] b', 0) != null)
			return 'mod';
		
		if($username->find('font[color=red]', 0) != null)
			return 'banned';
		
		if($username->find('strong', 0) != null)
			return 'garry';
		
		return 'blue';
	}
	
	function curl_get($page)
	{
		$return = parent::curl_get($page);
		
		if($return == 'The service is unavailable')
		{
			imsg('Facepunch is down, retrying in 10 seconds');
			sleep(10);
			$this->curl_get($page);
		}
	}
	
	function curl_post($page, $data)
	{
		$return = parent::curl_post($page, $data);
		
		if($return == 'The service is unavailable')
		{
			imsg('Facepunch is down, retrying in 10 seconds');
			sleep(10);
			$this->curl_post($page, $data);
		}
	}
}