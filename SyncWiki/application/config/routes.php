<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "page";
$route['scaffolding_trigger'] = "";

/**************************
** SyncWiki Routes Ahoy! **
**************************/

// Pages
// Must be first so that others acn override

$route['([^/]*?)'] = 'page/view/$1';
$route['([^/]*?)/edit'] = 'page/edit/$1';
$route['([^/]*?)/edit-([0-9a-zA-Z]*?)'] = 'page/edit/$1/$2';
$route['([^/]*?)/history'] = 'page/history/$1';
$route['([^/]*?)/view/(:num)'] = 'page/view/$1/$2';

// Auth

$route['auth'] = 'auth';

// System

$route['System'] = 'system';
$route['System/Page[ _]List'] = 'system/page_list';
$route['System/User[ _]List'] = 'system/user_list';

// Users

$route['User/([^/]*?)'] = 'user/view/$1';
$route['User/([^/]*?)/message'] = 'user/message/$1';

// AJAX

$route['ajax/page/update_protection'] = 'page/ajax_update_protection';
$route['ajax/page/delete'] = 'page/ajax_delete';
$route['ajax/toolbox_toggle'] = 'system/ajax_toolbox_update';

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */