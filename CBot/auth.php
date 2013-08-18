<?php

class authSystem {
	
	private $Owner;
	private $OwnerHost;
	private $Users;
	private $Groups;
	
	function __construct() {
		$this->Owner		= array();
		$this->OwnerHost	= array();
		$this->Users		= array();
		$this->Groups		= array();
	}
	
	function setOwner($Owner) {
		if(is_array($Owner)) {
			$this->Owner = array_merge($this->Owner, $Owner);
		} else {
			$this->Owner[] = $Owner;
		}
	}
	
	function setOwnerHost($OwnerHost) {
		if(is_array($OwnerHost)) {
			$this->OwnerHost = array_merge($this->OwnerHost, $OwnerHost);
		} else {
			$this->OwnerHost[] = $OwnerHost;
		}
	}
	
	function checkPermission($nick, $host, $permission="") {
		global $CBot;
		echo "Check permissions\n";
		echo "  Nick ".$nick."\n";
		echo "  Host ".$host."\n";
		echo "  Perm ".$permission."\n";
		if(in_array($nick, $this->Owner)) {
			echo "Nick in array\n";
			if(in_array($host, $this->OwnerHost)) {
				return true;
			} else {
				echo "Host not in array\n";
				foreach($this->OwnerHost as $hostregex) {
					if(substr($hostregex, 0, 1) == '/') {
						if (preg_match($hostregex, $host)) {
							return true;
						}
					}
				} 
			}
			$CBot->irc->notice($nick, 'Please set your host or stop impersonating the owner. Thank you.');
		}
		echo "Passed owner check\n";
		if($permission == "") {
			return false;
		}
		echo "Passed owner check2\n";
		if(in_array($nick, $this->Users)) {
			if($host != $this->Users['nick']['host']) {
				if(substr($this->Users['nick']['host'], 0, 1) == '/') {
					if (!preg_match($this->Users['nick']['host'], $host)) {
						return false;
					}
				} else {
					return false;
				}
			}
			
			$permissions = array();
			$currentGroup = '';
			do {
				if($currentGroup == '') {
					$currentGroup = $this->Users[$nick]['group'];
				} else {
					$currentGroup = $this->Groups[$currentGroup]['parent'];
				}
				$permissions = array_merge($permissions, $this->Groups[$currentGroup]['permissions']);
			} while ($this->Groups[$currentGroup]['parent'] !== '');
			if(in_array($permission, $permissions)) {
				return true;
			} else {
				return false;
			}
			
		} else {
			return false;
		}
		echo "Hitting failsafe!\n";
		//Failsafe
		return false;
	}
	
	function addUser($nick, $host, $group) {
		$this->Users[$nick] = array(
			'nick' => $nick,
			'host' => $host,
			'group' => $group
		);
	}
	
	function loadACL($file) {
		require_once($file);
		$this->Groups = array_merge($GLOBALS['aclGroups'], $this->Groups);
		unset($GLOBALS['aclGroups']);
	}
	
	function debug() {
		echo "Owner\n";
		print_r($this->Owner);
		echo "OwnerHost\n";
		print_r($this->OwnerHost);
		echo "Users\n";
		print_r($this->Users);
		echo "Groups\n";
		print_r($this->Groups);
		//echo "aclGroups\n";
		//require_once('cwiibot.acl.php');
		//print_r($GLOBALS['aclGroups']);
	}
}