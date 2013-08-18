<?php

// Mostly stolen from MyBB

class hookSystem {
	
	var $hooks;
	
	function load($plugin) {
		if($plugin != "" && file_exists("plugins/".$plugin.".php"))
		{
			require_once "plugins/".$plugin.".php";
			eval($plugin."_load();");
		}
	}
	
	function unload($plugin) {
		if($plugin != "" && file_exists("plugins/".$plugin.".php"))
		{
			require_once "plugins/".$plugin.".php";
			eval($plugin."_unload();");
		}
	}
	
	function addHook($hook, $function, $priority=10, $file="") {
		// Check to see if we already have this hook running at this priority
		if(!empty($this->hooks[$hook][$priority][$function]) && is_array($this->hooks[$hook][$priority][$function]))
		{
			return true;
		}

		// Add the hook
		$this->hooks[$hook][$priority][$function] = array(
			"function" => $function,
			"file" => $file
		);
		return true;
	}
	
	function removeHook($hook, $function, $file="", $priority=10)
	{
		// Check to see if we don't already have this hook running at this priority
		if(!isset($this->hooks[$hook][$priority][$function]))
		{
			return true;
		}
		unset($this->hooks[$hook][$priority][$function]);
	}
	
	function runHook($hook, $arguments="") {
		if(!@is_array($this->hooks[$hook]))
		{
			return $arguments;
		}
		if ($arguments == "") {
			$arguments = array('');
		}
		ksort($this->hooks[$hook]);
		foreach($this->hooks[$hook] as $priority => $hooks)
		{
			if(is_array($hooks))
			{
				foreach($hooks as $hook)
				{
					if($hook['file'])
					{
						require_once $hook['file'];
					}
					
					$returnargs = call_user_func_array($hook['function'], &$arguments);
					
					
					if($returnargs)
					{
						$arguments = $returnargs;
					}
				}
			}
		}
		return $arguments;
	}
}