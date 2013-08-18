<?php

include_once("simple_html_dom.php");

function count_events($user) {

    debug("In count events", "\n");
    $html = file_get_html('http://www.facepunch.com/fp_events.php?user='.$user);

    // All the events
    $r['ban'] = 0;
    $r['pban'] = 0;
    $r['unban'] = 0;
    $r['closed'] = 0;
    $r['opened'] = 0;
    $r['mov'] = 0;
    $r['rename'] = 0;
    $r['ddt'] = 0;
    $r['delsoft'] = 0;
    $r['capsfix'] = 0;

    $events_array = array ('ban', 'pban', 'unban', 'closed', 'closed', 'opened', 'mov',
    'rename', 'ddt', 'delsoft', 'capsfix');
    debug("Events and types declared");

    foreach ($html->find('div.eventtime') as $eventtime) {
        debug("In eventtime div foreach");
        debug("Eventtime plaintext is ".$eventtime->plaintext);
        if (preg_match('/(recent|\d{1,2}? hour(|s) ago).*/i', $eventtime->plaintext)) {
            debug("Matches regex!");
            foreach ($eventtime->find('div.event') as $event) {
                //echo "uhhh";
                $eimgs = $event->find('li a img');
                foreach($eimgs as $eimg) {
                    $tmp = explode('/', $eimg->src);
                    $tmp = explode('.', $tmp[3]);
                    if (in_array($tmp[0], $events_array)) {
                        $r[$tmp[0]]++;
                        debug("Event type is ".$tmp[0]);
                    }
                }
                //$events++;
            }
        }
        else {
            debug("No match, breaking");
            break;
        }
    }

    $html->clear();
    unset($html);
    debug("Cleaning html scraper");

    debug("Out of foreach, returning");

    return $r;
}

function debug($msg, $pre="", $post="\n") {
    global $msglvl;
    if($msglvl >= 3)
        echo $pre."!!DEBUG!! ".$msg.$post;
}

function warn($msg, $pre="", $post="\n") {
    global $msglvl;
    if($msglvl >= 2)
        echo $pre."!!WARN!! ".$msg.$post;
}

function info($msg, $pre="", $post="\n") {
    global $msglvl;
    if($msglvl >= 1)
        echo $pre.$msg.$post;
}

// Variables declared last.

/*
 * $msglvl:
 * 0 - No messages
 * 1 - Info only
 * 2 - Warnings
 * 3 - Debug information
 *
 * Default 1
 */
$msglvl = 1;
