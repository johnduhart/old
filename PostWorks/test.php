<?php

include "core.php";

$poster = new PostWorks();
/*
$poster->useForum('mybb');
$poster->baseUrl = "http://compwhizii.net/mybbdev/";
*/
/*$poster->useForum('vb');
$poster->useModule('facepunch');
$poster->baseUrl = "http://www.facepunch.com/";

$poster->modules->facepunch->ratePost('11324939', '3');*/

//$poster->forum->login('bot', 'lolbot');
//$poster->forum->newThread('2', 'Test thread', '4', 'This is a test of the threadworks posting robot.', 'lol, fff, ahhhh');
//$poster->forum->post('3', 'Lol you\'re funny', '', '');
//$poster->forum->postReply('13', '', 'Lol you\'re funny', '', '');
//$poster->forum->setSignature('Sup this is a signature');

$poster->useForum('facepunch');
$poster->useModule('facepunch');
$poster->baseUrl = "http://www.facepunch.com/";

print_r($poster->forum->getPosts('851420', '40'));
