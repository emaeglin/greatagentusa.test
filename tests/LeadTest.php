<?php
include_once dirname(__FILE__) . '/../core/init.php';

class LeadTest extends PHPUnit_Framework_TestCase
{
    private $config = array();

    public function __construct() 
    {
        global $google;
        global $twilio;
        global $DBconfig;
        $this->config = array(
            'google'    => $google,
            'twilio'    => $twilio,
            'DBconfig'  => $DBconfig
        );
    }

    public function testCanBeIncomplete() 
    {
        $Lead = new LeadModel($this->config);
        $this->assertEquals(false, $Lead->complete());
        
        $_POST['ClientPhone'] = "+1-541-754-3010";
        $_POST['ClientName'] = "Bohdan";
        $Lead = new LeadModel($this->config);
        $this->assertEquals(false, $Lead->complete());
        
        $_POST['ClientPhone'] = "1-754-3010";
        $_POST['ClientName'] = "Bohdan";
        $_POST['ClientEmail'] = "emaeglin@gmail.com";
        $Lead = new LeadModel($this->config);
        $this->assertEquals(false, $Lead->complete());
    }
    
    public function testCanBeComplete() 
    {
        $_POST['ClientPhone'] = "+1-541-754-3010";
        $_POST['ClientName'] = "Bohdan";
        $_POST['ClientEmail'] = "emaeglin@gmail.com";
        $Lead = new LeadModel($this->config);
        $this->assertEquals(true, $Lead->complete());
    }
    
    public function testSaveCorrect()
    {
    }
    
    public function testSaveIncorrect()
    {
    }
}