<?php

require_once("curl.php");
require_once("forums/base_forum.php");
require_once("modules/base_module.php");
require_once("simple_html_dom.php");

class PostWorks {
	
	// cUrl class
	public $cUrl;
	
	// Forum class
	public $forum;
	
	// Modules object
	public $modules;
	
	// Base URL for the forum, include a trailing slash
	public $baseUrl;
	
	function __construct() {
		$this->cUrl = new cUrl();
	}
	
	function useForum($forum) {
		require_once("forums/".$forum."_forum.php");
		eval("\$this->forum = new ".$forum."_forum(\$this);");
	}
	
	function useModule($module) {
		require_once("modules/".$module."_module.php");
		eval("\$this->modules->".$module." = new ".$module."_module(\$this);");
	}
}

function dmsg($msg)
{
	echo date(DATE_ATOM)." - Debug> ".$msg."\n";
}

function wmsg($msg)
{
	echo date(DATE_ATOM)." - Warn> ".$msg."\n";
}


function imsg($msg)
{
	echo date(DATE_ATOM)." - Info> ".$msg."\n";
}
