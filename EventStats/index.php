<?php
error_reporting(E_ALL);
// Entry point for the application
require("inc/config.php");
require("inc/core.php");
//echo "passed db";
session_start();
//echo $_GET['p'];

page_bye(); die;

if (!empty($_GET['p'])) {
	//echo "inswitch";
	$page_full = $_GET['p'];
	$pages = explode('/',$page_full);
	//echo " afterkaboom";
	//echo print_r($pages);

	//Do routing now
	switch($pages[0]) {
		case "mods":
			if (empty($pages[1])) {
				page_modlist();
			} else {
				page_modinfo($pages[1],$pages[2]);
			}
		break;
		case "alerts":
			page_alerts();
		break;
		case "awards":
			page_awards();
		break;
		case "sets":
			if (!empty($pages[1])) {
				page_sets($pages[1]);
			} else {
				page_sets();
			}
		break;
		case "login":
			if (empty($_POST['login'])) {
				page_login();
			} else {
				process_login();
			}
		break;
		case "logout":
			page_logout();
		break;
		case "admin":
			if (empty($pages[1])) {
				page_admin();
			} else {
				switch ($pages[1]) {
					case "mods":
						if (empty($pages[2])) {
							page_admin_mods();
						} else {
							switch ($pages[2]) {
								case "add":
									if (empty($_POST['add'])) {
										page_admin_mods_add();
									} else {
										process_admin_mods_add();
									}
								break;
								case "edit":
									if (empty($_POST['save'])) {
										page_admin_mods_edit($pages[3]);
									} else {
										process_admin_mods_edit();
									}
								break;
								case "del":
									if (empty($_POST['id'])) {
										page_admin_mods_del($pages[3]);
									} else {
										process_admin_mods_del();
									}
								break;
								default:
									page_404();
								break;
							}
						}
					break;
					case "users":
						if (empty($pages[2])) {
							page_admin_users();
						} else {
							switch ($pages[2]) {
								case "add":
									if (empty($_POST['add'])) {
										page_admin_users_add();
									} else {
										process_admin_users_add();
									}
								break;
								case "edit":
									if (empty($_POST['save'])) {
										page_admin_users_edit($pages[3]);
									} else {
										process_admin_users_edit();
									}
								break;
								case "del":
									if (empty($_POST['id'])) {
										page_admin_users_del($pages[3]);
									} else {
										process_admin_users_del();
									}
								break;
								default:
									page_404();
								break;
							}
						}
					break;
					case "sets":
						if (empty($pages[2])) {
							page_admin_sets();
						} else {
							switch ($pages[2]) {
								case "p":
									page_admin_sets($pages[3]);
								break;
								case "del":
									if (empty($_POST['id'])) {
										page_admin_sets_del($pages[3]);
									} else {
										process_admin_sets_del();
									}
								break;
								case "count":
									page_admin_sets_count();
								break;
							}
						}
					break;
					default:
						page_404();
					break;
				}
			}
		break;
		case "home":
			page_home();
		break;
		default:
			page_404();
		break;
	}
} else {
	page_home();
}
?>
