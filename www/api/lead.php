<?php

include_once '../../core/init.php';


$Lead = new LeadModel(array(
    'google'    => $google,
    'twilio'    => $twilio,
    'DBconfig'  => $DBconfig,
));


if ($Lead->Complete()) {
    if (!$Lead->Save()) {
        PageModel::Error($Lead->error);
    } else {
        echo "Lead ID: " . $Lead->Id;
        echo "<br>";
        PageModel::Success($Lead->UserData['IsCompanyPhone']);
    }
} else {
    PageModel::Error();
}
