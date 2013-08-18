<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parser {
	
	function parse($text)
	{
		// Pasre the page here! It's a complicated process but we can do it :)
		
		// Standardize line endings
		$fText = str_replace("\n\r", "\n", $text);
		
		// Perform an HTML clean
		$fText = htmlspecialchars($fText);
		
		// Bolds and THEN italics
		$this->_build_bolds($fText);
		$this->_build_italics($fText);
		
		// UL Lists
		preg_match_all('/(\*([^*\n]*?)\n)+/i', $fText, $result);
		foreach($result[0] as $list)
		{
			$this->_build_uls($fText, $list);
		}
		unset($result);
		// OL Lists
		preg_match_all('/(#([^#\n]*?)\n)+/i', $fText, $result);
		foreach($result[0] as $list)
		{
			$this->_build_ols($fText, $list);
		}
		unset($result);
		
		// Headers (== Level One ==)
		$this->_build_headers($fText);
		
		// Build external images
		// When we do uploading we'll add internal
		$this->_build_external_images($fText);
		
		// Build links
		$this->_build_internal_links($fText);
		$this->_build_external_links($fText);
		
		// Split into paragraphs
		$paras = explode("\n\n", $fText);
		$fText = '';
		foreach($paras as $para)
		{
			$fText .= '<p>'.$para.'</p>';
		}
		
		return $fText;
	}
	
	function _build_uls(&$fText, $list)
	{
		$items = explode('*', $list);
		unset($items[0]);
		$ul = "<ul>";
		foreach($items as $item)
		{
			$ul .= "<li>".trim($item)."</li>";
		}
		$ul .= "</ul>\n";
		$fText = str_replace($list, $ul, $fText);
	}
	
	function _build_ols(&$fText, $list)
	{
		$items = explode('#', $list);
		unset($items[0]);
		$ol = "<ol>";
		foreach($items as $item)
		{
			$ol .= "<li>".trim($item)."</li>";
		}
		$ol .= "</ol>\n";
		$fText = str_replace($list, $ol, $fText);
	}
	
	function _build_headers(&$fText)
	{
		preg_match_all('/=((?:=){1,4}?)([^=]+?)\1=/i', $fText, $result);
		foreach($result[0] as $id => $header)
		{
			$level = strlen($result[1][$id]);
			if($level > 3)
			{
				continue;
			}
			$h = "<h".$level.">".trim($result[2][$id])."</h".$level.">";
			$fText = str_replace($header, $h, $fText);
		}
	}
	
	function _build_internal_links(&$fText)
	{
		preg_match_all('/\[\[([^|\]]*?)(?:|\|([^|\]]*?))\]\]/i', $fText, $result);
		//print_r($result);die;
		foreach($result[0] as $id => $text)
		{
			$name = ($result[2][$id] != '') ? $result[2][$id] : $result[1][$id];
			$link = "<a href=\"".site_url($result[1][$id])."\">".$name."</a>";
			$fText = str_replace($text, $link, $fText);
		}
	}
	
	function _build_external_links(&$fText)
	{
		// Super complicated regex
		preg_match_all('/\[((?:(https?|ftp):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$])(| [^\]]*?)\]/i', $fText, $result);
		foreach($result[0] as $id => $text)
		{
			$name = ($result[3][$id] != '') ? $result[3][$id] : $result[1][$id];
			$protocol = ($result[2][$id] != '') ? '' : 'http://' ;
			$link = "<a href=\"".$protocol.$result[1][$id]."\">".$name."</a>";
			$fText = str_replace($text, $link, $fText);
		}
	}
	
	function _build_external_images(&$fText)
	{
		preg_match_all('/\[img:((?:(https?|ftp):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$])\]/i', $fText, $result);
		foreach($result[0] as $id => $text)
		{
			$protocol = ($result[2][$id] != '') ? '' : 'http://' ;
			$img = "<img src=\"".$protocol.$result[1][$id]."\" />";
			$fText = str_replace($text, $img, $fText);
		}
	}
	
	function _build_bolds(&$fText)
	{
		preg_match_all('/\'\'\'([^\'](.)*?)\'\'\'/', $fText, $result);
		foreach($result[0] as $id => $text)
		{
			$bold = "<strong>".$result[1][$id]."</strong>";
			$fText = str_replace($text, $bold, $fText);
		}
	}
	
	function _build_italics(&$fText)
	{
		preg_match_all('/\'\'([^\'](.)*?)\'\'/', $fText, $result);
		foreach($result[0] as $id => $text)
		{
			$italic = "<em>".$result[1][$id]."</em>";
			$fText = str_replace($text, $italic, $fText);
		}
	}
}