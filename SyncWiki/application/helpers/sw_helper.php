<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('edit_page_locked') )
{
	function edit_page_locked($lock_status)
	{
		$CI =& get_instance();
		if($lock_status == 0)
			return '';
		$who = ($lock_status == 1) ? 'unregisted users' : 'non-admins';
		$CI->load->vars('locked_who', $who);
		return $CI->load->view('sw/page/edit_locked', '', TRUE);
	}
}

if( ! function_exists('top_user_bar') )
{
	function top_user_bar()
	{
		$CI =& get_instance();
		
		if($CI->ion_auth->logged_in() === FALSE)
		{
			return $CI->load->view('sw/top_guest', '', TRUE);
		}
		else
		{
			$CI->load->vars('username', $CI->session->userdata('username'));
			return $CI->load->view('sw/top_logged_in', '', TRUE);
		}
	}
}

if( ! function_exists('alt_switcher') )
{
	function alt_switcher($seed, $low, $high)
	{
		global $alt_switcher;
		
		if(!isset($alt_switcher[$seed]))
		{
			$alt_switcher[$seed] = 1;
		}
		elseif($alt_switcher[$seed] < $high)
		{
			$alt_switcher[$seed]++;
		}
		else
		{
			$alt_switcher[$seed] = $low;
		}
		
		return $alt_switcher[$seed];
	}
}

if( ! function_exists('revision_type_icon') )
{
	function revision_type_icon($type, $prefix)
	{
		$icon = $prefix;
		switch($type)
		{
			case 'edit':
				$icon .= 'edit.png';
				break;
			case 'create':
				$icon .= 'add.png';
				break;
			case 'protection':
				$icon .= 'protection.png';
				break;
			case 'undelete':
				$icon .= 'un';
			case 'delete':
				$icon .= 'delete.png';
				break;
			default:
				$icon = '';
				break;
		}
		
		if($icon != '')
		{
			$image = "<img src=\"".$icon."\" /> ";
		}
		else
		{
			$image = '';
		}
		
		return $image;
	}
}

if( ! function_exists('syncwiki_version') )
{
	function syncwiki_version()
	{
		$CI =& get_instance();
		
		return $CI->config->item('syncwiki_version');
	}
}

function gravatar( $email, $rating = 'X', $size = '120', $default = 'http://gravatar.com/avatar.php' )
{
	// Hash the email address
	$email = md5( $email );
	
	// Return the generated URL
	return "http://gravatar.com/avatar.php?gravatar_id="
	    .$email."&amp;rating="
	    .$rating."&amp;size="
	    .$size."&amp;default="
	    .$default;
}