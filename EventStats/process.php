<?php

include_once "inc/config.php";
include_once "inc/core.php";
include_once "inc/eventStatsLib.php";

// Debugging messages for the count function:
$msglvl = 1;

info("Files included, beginning set processing...");

//Script was runing out of time, this is a workaround
info("Setting time limit to zero");
set_time_limit(0);

//Get the mods
$q = 'SELECT * FROM `mods` WHERE `active` = \'1\' ORDER BY `name`';
$mods = mysql_query($q);
info("Mods loaded");

// Get the set timestamp
$setstamp = time();
info("Set time loaded");

// Add a new set to the DB
mysql_query("INSERT INTO `sets` (`id`, `stamp`, `pruned`) VALUES ( NULL, '".$setstamp."', 0) ");
//And then get that set ID
$setid = mysql_fetch_array(mysql_query("SELECT * FROM `sets` ORDER BY `stamp` DESC LIMIT 1"));
$setid = $setid['id'];
info("Set added. Set id is ".$setid);

// Get the events for every mod
while ($mod = mysql_fetch_array($mods, MYSQL_BOTH)) {
    info("Processing ".$mod['name']."...","","");
    $e = count_events($mod['uid']);
    $q = "INSERT INTO `count` (`id`, `set`, `uid`, `ban`, `pban`,";
    $q.= " `unban`, `closed`, `opened`, `mov`, `rename`, `ddt`, `delsoft`, `capsfix`)";
    $q.= " VALUES (NULL, '".$setid."', '".$mod['id']."', '".$e['ban']."', '".$e['pban']."',";
    $q.= " '".$e['unban']."', '".$e['closed']."', '".$e['opened']."', '".$e['mov']."',";
    $q.= " '".$e['rename']."', '".$e['ddt']."', '".$e['delsoft']."', '".$e['capsfix']."');";
    mysql_query($q);

    info("Done!");
}

info("Processing complete");
info("Now preforming topstats");

include "process_tp.php";