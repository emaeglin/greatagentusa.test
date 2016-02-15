<?php
include_once dirname(__FILE__) . '/../core/init.php';

class UserTest extends PHPUnit_Framework_TestCase
{
    
    public function testCanBeIncomplete()
    {
        global $google;
        global $twilio;
        $config = array(
            'google'    => $google,
            'twilio'    => $twilio
        );
        
        $User = new UserModel($config);
        $this->assertEquals(false, $User->complete());
        
        
        $_POST['ClientPhone'] = "+1-541-754-3010";
        $_POST['ClientName'] = "Bohdan";
        $User = new UserModel($config);
        $this->assertEquals(false, $User->complete());
        
        
        $_POST['ClientPhone'] = "1-754-3010";
        $_POST['ClientName'] = "Bohdan";
        $_POST['ClientEmail'] = "emaeglin@gmail.com";
        $User = new UserModel($config);
        $this->assertEquals(false, $User->complete());
    }
    
    public function testCanBeComplete()
    {
        global $google;
        global $twilio;
        $config = array(
            'google'    => $google,
            'twilio'    => $twilio
        );
        
        $_POST['ClientPhone'] = "+1-541-754-3010";
        $_POST['ClientName'] = "Bohdan";
        $_POST['ClientEmail'] = "emaeglin@gmail.com";
        $User = new UserModel($config);
        $this->assertEquals(true, $User->complete());
    }
}