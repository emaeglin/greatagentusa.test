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
        PageModel::Success($Lead);
    }
} else {
    PageModel::Error("Incomplete");
}
