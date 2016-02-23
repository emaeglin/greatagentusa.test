<?php

require_once dirname(__FILE__) . '/../core/init.php';
require_once dirname(__FILE__) . '/../core/includes/vendors/twilio/Services/Twilio.php';

if (ScriptIsRunning("call.php", "maeglin")) {
    exit();
}


$Leads = new LeadsModel(array(
    'google'    => $google,
    'twilio'    => $twilio,
    'DBconfig'  => $DBconfig,
));

if ($Leads->GetLeadsForCalls()) {
    $Leads->StartAutoCalls();
}