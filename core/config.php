<?php

/*
 * GA_Lead 2 colums
    ALTER TABLE `GA_Lead` ADD `AutoCallComplete` tinyint(1) DEFAULT '0';
    ALTER TABLE `GA_Lead` ADD `AutoCallLocked` tinyint(1) DEFAULT '0';
 */

/* For Debug */
error_reporting(-1);
ini_set('display_errors', 'On');

date_default_timezone_set('America/Chicago');

$newLeadSource = "emaeglin test";

$twilio = array (
    'LookupUrl'    => 'https://lookups.twilio.com/v1/PhoneNumbers/%s',
    'AccountSID'   => 'ACefa52f762fcdc23583853810fb937dba',
    'AuthToken'    => '2621455c6bea1ccfbbdf12241b529185',
    'Version'      => '2010-04-01',
    'ValidatedPhone'    => '380682230411',
    
    'Voices'    => array (
        'FirstCall' => 'http://emaeglin.com/api/twilio/voice.php',
        'AutoCall' => 'http://emaeglin.com/api/twilio/voice_autocall.php',
    )
);

$twilioTest = array (
    'AccountSID'   => 'ACefa52f762fcdc23583853810fb937dba',
    'AuthToken'    => '2621455c6bea1ccfbbdf12241b529185',
);

$google = array(
    'ApiKey'        =>  'AIzaSyB-V9wfmLSJXkXAxlpdhvahiSh6zCuuBAw',
    'MapsApiUrl'    =>  'https://maps.googleapis.com/maps/api/place/nearbysearch/json?key=AIzaSyB-V9wfmLSJXkXAxlpdhvahiSh6zCuuBAw&language=en&location=39,-96&rankby=distance&keyword=%s',
    'SearchApiUrl'  =>  'https://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=%s&userip=%s'
);

$DBconfig = array (
    "host"      => "maeglin.mysql.ukraine.com.ua",
    "username"  => "maeglin_emaeglin",
    "passwd"    => "9vtccaps",
    "dbname"    => "maeglin_emaeglin",
);


function ScriptIsRunning($filename, $username)
{
    exec("ps -U $username -u $username u", $output, $result);
    $c = 0;
    foreach ($output AS $line) {
        if(strpos($line, "$filename")){
            $c++;
            if ($c > 1) {
                return true;
            }
        }
    }
    return false;
}