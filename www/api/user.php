<?php

include_once '../../core/init.php';


$User = new UserModel(array(
    'google'    => $google,
    'twilio'    => $twilio
));


if ($User->complete()) {
    $User->save();
    PageModel::Success($User->UserData['IsCompanyPhone']);
} else {
    PageModel::Error();
}
