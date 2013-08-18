<?php

namespace FPChat;
use FPChat\Plugin\AbstractPlugin;

class PluginBroker
{
	private $_plugins = array();

	public function addPlugin($code, AbstractPlugin $plugin)
	{
		$this->_plugins[$code] = $plugin;
	}

	public function __call($name, $arguments)
	{
		if (substr($name, 0, 2) != 'on')
		{
			throw new \Exception('Invalid method passed to plugin broker');
		}

		foreach ($this->_plugins AS $id => $plugin)
		{
			call_user_func_array(array($plugin, $name), $arguments);
		}
	}
}
