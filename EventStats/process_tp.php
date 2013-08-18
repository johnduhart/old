<?php

include_once "inc/config.php";
include_once "inc/core.php";
include_once "inc/eventStatsLib.php";

// Message level
$msglvl = 1;

// Array of all the events
$events_array = array ('ban', 'pban', 'unban', 'closed', 'closed', 'opened', 'mov',
'rename', 'ddt', 'delsoft', 'capsfix', 'total');

info("Shared files loaded");
info("Time limit is zero");
set_time_limit(0);

//Get the mods
$q = 'SELECT * FROM `mods` WHERE `active` = \'1\' ORDER BY `name`';
$mods = mysql_query($q);
info("Mods loaded");

while ($mod = mysql_fetch_array($mods, MYSQL_BOTH)) {
    info("Processing ".$mod['name']);
    $total = array();
    foreach($events_array as $evt) {
        $total[$evt] = 0;
    }

    $counts = mysql_query("SELECT * FROM `count` WHERE `uid` = '".$mod['id']."'");
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

    // Build the very awkward query...
    $q = "UPDATE `mods` SET ";
    // More refactoring becuase I'm too lazy to type
    foreach ($events_array as $evt) {
        $q.="`".$evt."` = '".$total[$evt]."', ";
    }
    // Cut off the extra comma
    $q = substr($q, 0, -2);
    $q.=" WHERE `id` = '".$mod['id']."' LIMIT 1";
    mysql_query($q);
    info("Done!");
}

info("All done!");