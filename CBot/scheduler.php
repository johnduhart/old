<?php

class scheduler {
	
	private $schedule = array();
	
	function addEvent($function, $time) {
		if(@in_array($function, $this->schedule[$time])) {
			return;
		}
		
		$this->schedule[$time][] = $function;
	}
	
	function removeEvent($function, $time) {
		if(!in_array($function, $this->schedule[$time])) {
			return;
		}
		
		$this->schedule[$time][$function] = null;
	}
	
	function checkEvents() {
		$curTime = time();
		$return = false;
		
		foreach($this->schedule as $time => $tasks) {
			if($time <= $curTime) {
				foreach($tasks as $task) {
					$return = true;
					call_user_func($task);
				}
				unset($this->schedule[$time]);
			}
		}
		return $return;
	}
	
	function echoEvents() {
		print_r($this->schedule);
	}
}