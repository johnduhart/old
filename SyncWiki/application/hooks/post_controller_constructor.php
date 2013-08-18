<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function toolbox_prep()
{
	$CI =& get_instance(); 
	
	$show_toolbox = ($CI->session->userdata('show_toolbox') == '1');
	$CI->load->vars(array(
		'show_toolbox' => $show_toolbox,
		'hide_toolbox' => false
	));
}