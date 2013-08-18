<?php

class MY_Input extends CI_Input {
	
	// Facepunch is a complete retard with cookies and triggers this in FF
	// Added '%' to regex fixes it
	function _clean_input_keys($str)
	{
		if ( ! preg_match("/^[%a-z0-9:_\/-]+$/i", $str))
		{
			//print_r($_COOKIE);
			exit('Disallowed Key Characters: '.$str);
		}

		return $str;
	}
}