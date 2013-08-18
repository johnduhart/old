<?php

require_once './init.php';

use FPChat\Bot;

$bot = new Bot();
$bot->login($config['username'], $config['password']);
$bot->registerPlugin('admin', new FPChat\Plugin\Admin(89526));
$bot->registerPlugin('console', new FPChat\Plugin\ConsolePrint);
$bot->registerPlugin('logs', new FPChat\Plugin\Logs\Logs);
//$bot->registerPlugin('logsquery', new FPChat\Plugin\LogQuery);

//pcntl_signal(SIGTERM, array($bot. 'stop'));
//pcntl_signal(SIGINT, array($bot. 'stop'));

$bot->run();