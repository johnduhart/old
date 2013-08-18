<?php

mysql_connect($dbHost, $dbUser, $dbPass) or die("MySQL Error: " . mysql_error());
mysql_select_db($dbName) or die("MySQL Error: " . mysql_error());

function getUrl($uri) {
	global $baseUrl;
	return str_replace(' ','%20',$baseUrl.$uri);
}

function check_auth($action,$username="") {
	if ($username == "") {
		if (empty($_SESSION['loggedin'])) {
			return false;
		} else {
			$username = $_SESSION['username'];
		}
	}
	
	$r = mysql_query("SELECT username FROM users WHERE username = '".$username."' AND can_".$action." = 1 LIMIT 1");
	return mysql_num_rows($r);
}

/*
* CORE PAGES
* General info pages
*/

function page_bye() {
	$title = "";
	include("pages/header.temp.php");
	include("pages/bye.temp.php");
	include("pages/footer.temp.php");
}

function page_home() {
	$title = "";
	include("pages/header.temp.php");
	include("pages/home.temp.php");
	include("pages/footer.temp.php");
}

function page_modlist() {
	$title = "Mod list";
	$q = 'SELECT * FROM mods WHERE active = 1 ORDER BY `name`';
	$active = mysql_query($q);
	
	if(check_auth('editmods')) {
		$q = 'SELECT * FROM mods WHERE active = 0 ORDER BY `name`';
		$unactive = mysql_query($q);
	}
	
	include("pages/header.temp.php");
	include("pages/modlist.temp.php");
	include("pages/footer.temp.php");
}

function page_modinfo($name,$id) {
	if (strtolower($name) != "id") {
		$q = "SELECT * FROM mods WHERE `name` = '".mysql_real_escape_string($name)."' LIMIT 1";
	} else {
		$q = "SELECT * FROM mods WHERE `id` = '".mysql_real_escape_string($id)."' LIMIT 1";
	}
	$r = mysql_query($q);
	
	if (mysql_num_rows($r) == 0) {
		return page_404_mod();
	}
	
	$modInfo = mysql_fetch_array($r);
	$modName = $modInfo['name'];
	$modId = $modInfo['id'];
	$modUid = $modInfo['uid'];
	$modActive = $modInfo['active'];
	$modRole = $modInfo['role'];
	$title = $modName." - Mod info";
	
	// Pull stats
	
	// Totals
	$totals = mysql_fetch_array(mysql_query("SELECT * FROM `mods` WHERE `id` = '".$modId."' LIMIT 1"));
	// Last 10 stats
	$l10 = mysql_query("SELECT `count`.*, `sets`.`stamp`, `sets`.`id` AS `set_id` FROM `count`,`sets` WHERE `sets`.`id` = `count`.`set` AND `count`.`uid` = '".$modId."' ORDER BY `count`.`set` DESC LIMIT 10");
	// For the graph.
	$l5 = mysql_query("SELECT `count`.*, `sets`.`stamp` FROM `count`,`sets` WHERE `sets`.`id` = `count`.`set` AND `count`.`uid` = '".$modId."' ORDER BY `count`.`set` DESC LIMIT 5");
	
	include("gchart.php");
	include("pages/header.temp.php");
	include("pages/modinfo.temp.php");
	include("pages/footer.temp.php");
}

function page_alerts() {
	$title = "Alerts";
	// Alert types
	$low_activity = array();
	$low_quota = array();
	$mia = array();
	
	// Array of all the events
	$events_array = array ('ban', 'pban', 'unban', 'closed', 'opened', 'mov',
'rename', 'ddt', 'delsoft', 'capsfix', 'total');
	
	$q = 'SELECT * FROM `mods` WHERE `active` = \'1\' ORDER BY `name`';
	$mods = mysql_query($q);
	
	while ($mod = mysql_fetch_array($mods, MYSQL_BOTH)) {
		$total = array();
		foreach($events_array as $evt) {
			$total[$evt] = 0;
		}
	
		$counts = mysql_query("SELECT `count`.*, `sets`.`stamp` FROM `count`, `sets` WHERE `sets`.`id` = `count`.`set` AND `count`.`uid` = '".$mod['id']."' ORDER BY `sets`.`stamp` DESC LIMIT 5");
		while ($count = mysql_fetch_array($counts)) {
			foreach ($count as $k => $v ) {
				if (in_array($k, $events_array)) {
					// What the fuck
					$total[$k] = $total[$k] + $v;
				}
			}
		}
	
		// I have no idea where, but theres odd numbers at the end of the array
		// so this flushes them out.
		$total[0] = 0;
	
		//echo print_r($total); die;
		// Total the...totals?
		foreach ($total as $k => $v)
		{
			$total['total'] = $total['total'] + $v;
		}
	
		if($total['total'] == 0) {
			$low_activity[$mod['name']] = $mod['id'];
		}
		
		if($total['total'] < 25) {
			$low_quota[$mod['name']] = $mod['id'];
		}
	}
	
	$q = 'SELECT * FROM `mods` WHERE `active` = \'1\' ORDER BY `name`';
	$mods = mysql_query($q);
	
	while ($mod = mysql_fetch_array($mods, MYSQL_BOTH)) {
		$total = array();
		foreach($events_array as $evt) {
			$total[$evt] = 0;
		}
	
		$counts = mysql_query("SELECT `count`.*, `sets`.`stamp` FROM `count`, `sets` WHERE `sets`.`id` = `count`.`set` AND `count`.`uid` = '".$mod['id']."' ORDER BY `sets`.`stamp` DESC LIMIT 14");
		while ($count = mysql_fetch_array($counts)) {
			foreach ($count as $k => $v ) {
				if (in_array($k, $events_array)) {
					// What the fuck
					$total[$k] = $total[$k] + $v;
				}
			}
		}
	
		// I have no idea where, but theres odd numbers at the end of the array
		// so this flushes them out.
		$total[0] = 0;
	
		//echo print_r($total); die;
		// Total the...totals?
		foreach ($total as $k => $v)
		{
			$total['total'] = $total['total'] + $v;
		}
	
		if($total['total'] == 0) {
			$mia[$mod['name']] = $mod['id'];
		}
	}
	
	
	include("pages/header.temp.php");
	include("pages/alerts.temp.php");
	include("pages/footer.temp.php");
}

function page_awards() {
	$title = "Top Mods";
	
	$top_banners = array();
	$top_pbanners = array();
	$top_closers = array();
	$top_movers = array();
	$top_renames = array();
	$top_ddters = array();
	
	// Top banners
	$q = mysql_query("SELECT `name`, `ban`, `id` FROM `mods` ORDER BY `ban` DESC LIMIT 5");
	while ($r = mysql_fetch_array($q)) {
		$top_banners[$r['name']] = $r['ban'];
	}
	
	// Top permabanners
	$q = mysql_query("SELECT `name`, `pban`, `id` FROM `mods` ORDER BY `pban` DESC LIMIT 5");
	while ($r = mysql_fetch_array($q)) {
		$top_pbanners[$r['name']] = $r['pban'];
	}
	
	// Top closers
	$q = mysql_query("SELECT `name`, `closed`, `id` FROM `mods` ORDER BY `closed` DESC LIMIT 5");
	while ($r = mysql_fetch_array($q)) {
		$top_closers[$r['name']] = $r['closed'];
	}
	
	// Top movers
	$q = mysql_query("SELECT `name`, `mov`, `id` FROM `mods` ORDER BY `mov` DESC LIMIT 5");
	while ($r = mysql_fetch_array($q)) {
		$top_movers[$r['name']] = $r['mov'];
	}
	
	// Top movers
	$q = mysql_query("SELECT `name`, `rename`, `id` FROM `mods` ORDER BY `rename` DESC LIMIT 5");
	while ($r = mysql_fetch_array($q)) {
		$top_renames[$r['name']] = $r['rename'];
	}
	
	// Top DDTs
	$q = mysql_query("SELECT `name`, `ddt`, `id` FROM `mods` ORDER BY `ddt` DESC LIMIT 5");
	while ($r = mysql_fetch_array($q)) {
		$top_ddters[$r['name']] = $r['ddt'];
	}
	
	include("pages/header.temp.php");
	include("pages/topmods.temp.php");
	include("pages/footer.temp.php");
}

function page_sets($set=-1) {
	// Get an array of id -> name for mods
	$q = 'SELECT * FROM `mods` ORDER BY `name`';
	$mods = mysql_query($q);
	$modArray = array();
	while ($mod = mysql_fetch_array($mods, MYSQL_BOTH))
		$modArray[$mod['id']] = $mod['name'];
		
	// Figure out the ID for the set.
	// If no set is giving, just use the latest.
	if ($set == -1) {
		$setinfo = mysql_fetch_array(mysql_query("SELECT * FROM `sets` ORDER BY `stamp` DESC LIMIT 1"));
		$setid = $setinfo['id'];
		$setstamp = $setinfo['stamp'];
		$setpruned = $setinfo['pruned'];
	} else {
		$setid = mysql_real_escape_string($set);
		$setinfo = mysql_fetch_array(mysql_query("SELECT * FROM `sets` WHERE `id` = '".$setid."' LIMIT 1"));
		$setstamp = $setinfo['stamp'];
		$setpruned = $setinfo['pruned'];
	}
	
	if(!$setpruned) {
		// Totals.
		$total['ban'] = 0;
		$total['pban'] = 0;
		$total['unban'] = 0;
		$total['closed'] = 0;
		$total['opened'] = 0;
		$total['mov'] = 0;
		$total['rename'] = 0;
		$total['ddt'] = 0;
		$total['delsoft'] = 0;
		$total['capsfix'] = 0;
		$total['total'] = 0;
		
		// Get the records
		$counts = mysql_query("SELECT * FROM `count` WHERE `set` = '".$setid."'");
	}
	
	$title = "Set #".$setid;
	include("pages/header.temp.php");
	include("pages/set.temp.php");
	include("pages/footer.temp.php");
}

function page_404() {
	$title = "404 not found";
	include("pages/header.temp.php");
	include("pages/404.temp.php");
	include("pages/footer.temp.php");
}

function page_404_mod() {
	$title = "Moderator not found";
	include("pages/header.temp.php");
	include("pages/404.mod.temp.php");
	include("pages/footer.temp.php");
}


/*
* BACKEND PAGES
* Administration pages
*/


function page_login($error="") {
	$title = "Login";
	include("pages/header.temp.php");
	include("pages/login.temp.php");
	include("pages/footer.temp.php");
}

function process_login() {
	if(empty($_POST['username']) || empty($_POST['password'])) {
		return page_login("Please supply both a username and password");
	}
	$username = mysql_real_escape_string($_POST['username']);
	$password = sha1(mysql_real_escape_string($_POST['password']));
	
	//echo $password;die;
	
	$q = "SELECT * FROM users WHERE username='".$username."' AND password='".$password."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_login("Incorrect username and password");
	}
	
	if(!check_auth('login',$username)) {
		return page_login("Login disallowed");
	}
	
	$_SESSION['loggedin'] = true;
	$_SESSION['username'] = $username;
	
	return page_home();
}

function page_logout() {
	$_SESSION = array();
	session_destroy();
	return page_info("Logged out", "Logged out", "You have been successfully logged out.<br /><a href=\"".getUrl("home")."\">Go home</a>");
}

function page_info($title, $pageName, $pageText, $pageIcon="info.png", $headerinclude="") {
	include("pages/header.temp.php");
	include("pages/infopage.temp.php");
	include("pages/footer.temp.php");
}

function page_deny() {
	return page_info("Access denied", "Access denied", "What are you doing?", "lock.png");
}

function page_admin() {
	if(!check_auth('login')) {
		return page_deny();
	}
	$title = "Admin Control Panel";
	include("pages/header.temp.php");
	include("pages/admin.temp.php");
	include("pages/footer.temp.php");
}


/*
* MODERATORS ADMIN
*
*/


function page_admin_mods() {
	if(!check_auth('login')) {
		return page_deny();
	}
	$title = "Mods - ACP";
	$q = 'SELECT * FROM mods ORDER BY `name`';
	$r = mysql_query($q);
	
	include("pages/header.temp.php");
	include("pages/admin.mods.temp.php");
	include("pages/footer.temp.php");
}

function page_admin_mods_edit($id,$error="") {
	if(!check_auth('login') || !check_auth('editmods')) {
		return page_deny();
	}
	
	//echo $pname; die;
	
	$q = "SELECT * FROM mods WHERE `id` = '".mysql_real_escape_string($id)."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_info("Mod not found", "Mod not found", "Bad ID", "error.png");
	}
	
	$modInfo = mysql_fetch_array($r);
	//echo print_r($modInfo); die;
	$username = $modInfo['name'];
	$modid = $modInfo['id'];
	$userid = $modInfo['uid'];
	$userrole = $modInfo['role'];
	$active = $modInfo['active'];
	
	$title = "Edit ".$username." - ACP";
	include("pages/header.temp.php");
	include("pages/admin.mods.edit.temp.php");
	include("pages/footer.temp.php");
}

function process_admin_mods_edit() {
	if(!check_auth('login') || !check_auth('editmods')) {
		return page_deny();
	}
	
	if(empty($_POST['username']) || empty($_POST['uid']) || empty($_POST['id'])) {
		return page_admin_mods_edit($_POST['id'],"Please supply a valid username and ID");
	}
	
	if(preg_match( '/^\d*$/'  , $_POST['uid']) != 1) {
		return page_admin_mods_edit($_POST['id'],"Please supply a valid ID");
	}
	
	$id = mysql_real_escape_string($_POST['id']);
	$username = mysql_real_escape_string($_POST['username']);
	$uid = mysql_real_escape_string($_POST['uid']);
	$role = mysql_real_escape_string($_POST['role']);
	$active = empty($_POST['active']);
	
	//echo mysql_num_rows(mysql_query("SELECT name FROM mods WHERE id='".$id."'")); die;
	
	if(mysql_num_rows(mysql_query("SELECT name FROM mods WHERE id='".$id."'")) == 0) {
		return page_info("Mod not found", "Mod not found", "Bad ID", "error.png");
	}
	
	//echo mysql_num_rows(mysql_query("SELECT name FROM mods WHERE name='".$username."'")); die;
	
	if(mysql_num_rows(mysql_query("SELECT name FROM mods WHERE name='".$username."'")) > 1) {
		return page_admin_mods_edit($id, "That username is already in use");
	}
	
	$q = "UPDATE mods SET name='".$username."', uid='".$uid."', active='";
	if (!$active) { $q .= "1', "; } else { $q .= "0', "; }
	$q .= "role='".$role."' WHERE id='".$id."'";
	mysql_query($q);
	
	return page_info("Moderator edited!", "Moderator edited!", "You will now be redirected back to the mod list", "check.png", "<META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=".getUrl('admin/mods')."\">");
}

function page_admin_mods_add($error="",$username="",$userid="",$userrole="") {
	if(!check_auth('login') || !check_auth('editmods')) {
		return page_deny();
	}
	
	$title = "Add Mod - ACP";
	include("pages/header.temp.php");
	include("pages/admin.mods.add.temp.php");
	include("pages/footer.temp.php");
}

function process_admin_mods_add() {
	if(!check_auth('login') || !check_auth('editmods')) {
		return page_deny();
	}
	
	if(empty($_POST['username']) || empty($_POST['uid'])) {
		return page_admin_mods_add("Please supply a username and user ID", $_POST['username'], $_POST['uid'], $_POST['role']);
	}
	
	if(preg_match( '/^\d*$/'  , $_POST['uid']) != 1) {
		return page_admin_mods_add("Please supply a vaild user ID", $_POST['username'], $_POST['uid'], $_POST['role']);
	}
	
	$username = mysql_real_escape_string($_POST['username']);
	$userid = mysql_real_escape_string($_POST['uid']);
	$userrole = mysql_real_escape_string($_POST['role']);
	
	if(mysql_num_rows(mysql_query("SELECT name FROM mods WHERE name='".$username."'")) > 0) {
		return page_admin_mods_add("That username is already in use", $_POST['username'], $_POST['uid'], $_POST['role']);
	}
	
	$q = "INSERT INTO `mods` (`name`, `uid`, `role`) VALUES ('".$username."', '".$userid."', '".$userrole."') ;";
	$r = mysql_query($q);
	
	return page_info("Success!", "Success!", $username." was added to the tracker! <a href=\"".getUrl('admin/mods')."\">Go to mod list</a> or <a href=\"".getUrl('admin/mods/add')."\">Add another</a>", $pageIcon="check.png");
}

function page_admin_mods_del($id) {
	if(!check_auth('login') || !check_auth('editmods')) {
		return page_deny();
	}
	
	$q = "SELECT * FROM mods WHERE `id` = '".mysql_real_escape_string($id)."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_info("Mod not found", "Mod not found", "Bad ID", "error.png");
	}
	
	$modInfo = mysql_fetch_array($r);
	//echo print_r($modInfo); die;
	$username = $modInfo['name'];
	$modid = $modInfo['id'];
	$userid = $modInfo['uid'];
	
	$title = "Delete Mod - ACP";
	include("pages/header.temp.php");
	include("pages/admin.mods.del.temp.php");
	include("pages/footer.temp.php");
}

function process_admin_mods_del() {
	if(!check_auth('login') || !check_auth('editmods')) {
		return page_deny();
	}
	
	if(!empty($_POST['no'])) {
		return page_admin_mods();
	}
	
	$id = mysql_real_escape_string($_POST['id']);
	
	// Let's check that they exist
	$q = "SELECT * FROM mods WHERE `id` = '".$id."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_info("Mod not found", "Mod not found", "Bad ID", "error.png");
	}
	$modInfo = mysql_fetch_array($r);
	
	// Kill their stats
	mysql_query("DELETE FROM count WHERE uid='".$id."'");
	// Kill their user
	mysql_query("DELETE FROM mods WHERE id='".$id."'");
	
	return page_info("Moderator Deleted", "Moderator Deleted", "Moderator ".$modInfo['name']." is no longer with us. <a href=\"".getUrl('admin/mods')."\">Go to mod list</a>", "check.png");
}


/*
* USERS ADMIN
*
*/


function page_admin_users() {
	if(!check_auth('login')) {
		return page_deny();
	}
	$title = "Users - ACP";
	$q = 'SELECT * FROM users ORDER BY `username`';
	$r = mysql_query($q);
	
	include("pages/header.temp.php");
	include("pages/admin.users.temp.php");
	include("pages/footer.temp.php");
}

function _can_icon($act) {
	if ($act != 0) {
		echo "<img src=\"images/check_small.png\" alt=\"Yes\" />";
	} else {
		echo "<img src=\"images/error_small.png\" alt=\"NO\" />";
	}
}

function page_admin_users_edit($id,$error="") {
	if(!check_auth('login') || !check_auth('editusers')) {
		return page_deny();
	}
	
	$q = "SELECT * FROM users WHERE `id` = '".mysql_real_escape_string($id)."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_info("User not found", "User not found", "Bad ID", "error.png");
	}
	
	$userInfo = mysql_fetch_array($r);
	//echo print_r($userInfo); die;
	$username = $userInfo['username'];
	$userid = $userInfo['id'];
	$email = $userInfo['email'];
	$can_login = $userInfo['can_login'];
	$can_editmods = $userInfo['can_editmods'];
	$can_editusers = $userInfo['can_editusers'];
	$can_editsets = $userInfo['can_editsets'];
	
	$title = "Edit ".$username." - ACP";
	include("pages/header.temp.php");
	include("pages/admin.users.edit.temp.php");
	include("pages/footer.temp.php");
}

function process_admin_users_edit() {
	if(!check_auth('login') || !check_auth('editusers')) {
		return page_deny();
	}
	
	if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['id'])) {
		return page_admin_users_edit($_POST['id'],"Please supply a valid username and email");
	}
	
	$id = mysql_real_escape_string($_POST['id']);
	$username = mysql_real_escape_string($_POST['username']);
	$email = mysql_real_escape_string($_POST['email']);
	$newpass = mysql_real_escape_string($_POST['newpass']);
	$can_login = empty($_POST['can_login']);
	$can_editmods = empty($_POST['can_editmods']);
	$can_editusers = empty($_POST['can_editusers']);
	$can_editsets = empty($_POST['can_editsets']);
	
	if(mysql_num_rows(mysql_query("SELECT username FROM users WHERE id='".$id."'")) == 0) {
		return page_info("Mod not found", "Mod not found", "Bad ID", "error.png");
	}
	
	$q = "UPDATE users SET username='".$username."', email='".$email."', ";
	if (!$can_login) { $q .= "can_login='1', "; } else { $q .= "can_login='0', "; }
	if (!$can_editmods) { $q .= "can_editmods='1', "; } else { $q .= "can_editmods='0', "; }
	if (!$can_editsets) { $q .= "can_editsets='1', "; } else { $q .= "can_editsets='0', "; }
	if (!$can_editusers) { $q .= "can_editusers='1'"; } else { $q .= "can_editusers='0'"; }
	if ($newpass != '') { $q .= ", password='".sha1($newpass)."'"; }
	$q .= " WHERE id='".$id."'";
	
	//echo $q; die;
	mysql_query($q);
	
	return page_info("User edited!", "User edited!", "You will now be redirected back to the user list", "check.png", "<META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=".getUrl('admin/users')."\">");
}

function page_admin_users_add($error="",$username="",$password="",$email="",$can_login=false,$can_editmods=false,$can_editusers=false,$can_editsets=false) {
	if(!check_auth('login') || !check_auth('editusers')) {
		return page_deny();
	}
	
	$title = "Add User - ACP";
	include("pages/header.temp.php");
	include("pages/admin.users.add.temp.php");
	include("pages/footer.temp.php");
}

function process_admin_users_add() {
	if(!check_auth('login') || !check_auth('editusers')) {
		return page_deny();
	}
	
	$username = mysql_real_escape_string($_POST['username']);
	$email = mysql_real_escape_string($_POST['email']);
	$password = sha1(mysql_real_escape_string($_POST['pass']));
	$can_login = empty($_POST['can_login']);
	$can_editmods = empty($_POST['can_editmods']);
	$can_editusers = empty($_POST['can_editusers']);
	$can_editsets = empty($_POST['can_editsets']);
	
	if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['pass'])) {
		return page_admin_users_add("Please supply a valid username, password and email",$_POST['username'],$_POST['pass'],$_POST['email'],
		!$can_login,!$can_editmods,!$can_editusers,!$can_editsets);
	}
	
	if(mysql_num_rows(mysql_query("SELECT username FROM users WHERE username='".$username."'")) > 0) {
		return page_admin_users_add("That username is already in use",$_POST['username'],$_POST['pass'],$_POST['email'],
		!$can_login,!$can_editmods,!$can_editusers,!$can_editsets);
	}
	
	$q = "INSERT INTO `users` (`username`, `password`, `email`, `can_login`, `can_editmods`, `can_editusers`, `can_editsets`) ";
	$q .= "VALUES ('".$username."', '".$password."', '".$email."',";
	if (!$can_login) { $q .= "'1', "; } else { $q .= "'0', "; }
	if (!$can_editmods) { $q .= "'1', "; } else { $q .= "'0', "; }
	if (!$can_editusers) { $q .= "'1', "; } else { $q .= "'0', "; }
	if (!$can_editsets) { $q .= "'1'"; } else { $q .= "'0'"; }
	$q .= ")";
	
	mysql_query($q);
	
	return page_info("User created!", "User created!", "You will now be redirected back to the user list", "check.png", "<META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=".getUrl('admin/users')."\">");
}

function page_admin_users_del($id) {
	if(!check_auth('login') || !check_auth('editusers')) {
		return page_deny();
	}
	
	$q = "SELECT * FROM users WHERE `id` = '".mysql_real_escape_string($id)."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_info("User not found", "User not found", "Bad ID", "error.png");
	}
	
	$userInfo = mysql_fetch_array($r);
	//echo print_r($modInfo); die;
	$username = $userInfo['username'];
	$userid = $userInfo['id'];
	
	$title = "Delete User - ACP";
	include("pages/header.temp.php");
	include("pages/admin.users.del.temp.php");
	include("pages/footer.temp.php");
}

function process_admin_users_del() {
	if(!check_auth('login') || !check_auth('editusers')) {
		return page_deny();
	}
	
	if(!empty($_POST['no'])) {
		return page_admin_users();
	}
	
	$id = mysql_real_escape_string($_POST['id']);
	
	// Let's check that they exist
	$q = "SELECT * FROM users WHERE `id` = '".$id."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_info("User not found", "User not found", "Bad ID", "error.png");
	}
	$userInfo = mysql_fetch_array($r);
	
	// Kill their user
	mysql_query("DELETE FROM users WHERE id='".$id."'");
	
	return page_info("User Deleted", "User Deleted", "User ".$userInfo['username']." is no longer with us. <strong><a href=\"".getUrl('admin/users')."\">Go to user list</a></strong>", "check.png");
}


/*
* SET ADMIN
*
*/


function page_admin_sets($page=1) {
	if(!check_auth('login')) {
		return page_deny();
	}
	
	$title = "Sets - ACP";
	$total = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS total FROM sets"));
	$total = $total['total'];
	$pages = ceil($total / 20);
	
	$start = ($page - 1) * 20;
	
	$q = 'SELECT * FROM sets ORDER BY `id` DESC LIMIT '.$start.', 20';
	$r = mysql_query($q);
	
	$page_links = "";
	
	for($i = 1; $i <= $pages; $i++) {
		if($i == $page) {
			$page_links .= "<strong>".$i."</strong> ";
		} else {
			$page_links .= "<a href=\"".getUrl('admin/sets/p/'.$i)."\">".$i."</a> ";
		}
	}
	
	include("pages/header.temp.php");
	include("pages/admin.sets.temp.php");
	include("pages/footer.temp.php");
}

function _set_status($status) {
	if ($status == 0) {
		echo "Okay";
	} elseif ($status == 1) {
		echo "<strong>Pruned</strong>";
	}
}

function page_admin_sets_del($id) {
	if(!check_auth('login') || !check_auth('editsets')) {
		return page_deny();
	}
	
	$q = "SELECT * FROM sets WHERE `id` = '".mysql_real_escape_string($id)."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_info("Set not found", "Set not found", "Bad ID", "error.png");
	}
	
	$title = "Delete Set - ACP";
	
	include("pages/header.temp.php");
	include("pages/admin.sets.del.temp.php");
	include("pages/footer.temp.php");
}

function process_admin_sets_del() {
	if(!check_auth('login') || !check_auth('editsets')) {
		return page_deny();
	}
	
	if(!empty($_POST['no'])) {
		return page_admin_sets();
	}
	
	$id = mysql_real_escape_string($_POST['id']);
	
	// Let's check that it exists
	$q = "SELECT * FROM sets WHERE `id` = '".$id."' LIMIT 1";
	$r = mysql_query($q);
	
	if(mysql_num_rows($r) == 0) {
		return page_info("Set not found", "Set not found", "Bad ID", "error.png");
	}
	
	// Kill the count records
	mysql_query("DELETE FROM count WHERE set='".$id."'");
	// Mark the set as pruned
	mysql_query("UPDATE sets SET pruned='1' WHERE id='".$id."'");
	
	return page_info("Set pruned", "Set pruned", "Set #".$id." has been pruned. <strong><a href=\"".getUrl('admin/sets')."\">Go to sets list</a></strong>", "check.png");
}

function page_admin_sets_count() {
	if(!check_auth('login') || !check_auth('editsets')) {
		return page_deny();
	}
	
	include("pages/header.temp.php");
	include("pages/admin.sets.count.temp.php");
	include("pages/footer.temp.php");
}
