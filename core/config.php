<?php

/* For Debug */
error_reporting(-1);
ini_set('display_errors', 'On');

$twilio = array (
    'LookupUrl'    => 'https://lookups.twilio.com/v1/PhoneNumbers/%s',
    'AccountSID'   => 'ACefa52f762fcdc23583853810fb937dba',
    'AuthToken'    => '2621455c6bea1ccfbbdf12241b529185',
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