<?php
//2
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
    
    public function testGetLead()
    {
        $Lead = new LeadModel(
            $this->config,
            24437
        );
        $this->assertEquals(24437, $Lead->Id);
        
        $Lead = new LeadModel(
            $this->config,
            1
        );
        $this->assertEquals(0, $Lead->Id);
    }
    
    public function testLeadExists()
    {
        $_POST['ClientPhone'] ="+380991133231";
        $_POST['ClientEmail'] ="nwenwne@gmail.com";
        $_POST['ClientName'] ="NEW";
        
        $Lead = new LeadModel(
            $this->config
        );
        
        $this->assertEquals(false, $Lead->LeadExists());
        
        
        $_POST['ClientPhone'] ="380-111-111111";
        $_POST['ClientEmail'] ="emaeglin@gmail.com";
        $_POST['ClientName'] ="Old";
        
        $Lead = new LeadModel(
            $this->config
        );
        
        $this->assertEquals(true, $Lead->LeadExists());
    }
}