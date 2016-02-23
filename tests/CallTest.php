<?php
//2
include_once dirname(__FILE__) . '/../core/init.php';
require_once dirname(__FILE__) . '/../core/includes/vendors/twilio/Services/Twilio.php';

class CallTest extends PHPUnit_Framework_TestCase
{
    private $config = array();
    private $Lead = null;
    private $Call = null;

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
        
        $this->Lead = new LeadModel(
            $this->config,
            24437
        );
        
        $this->Call = new CallModel(
            $this->config['twilio'], 
            $this->Lead->UserData, 
            $this->Lead->SalesPerson, 
            $this->config['DBconfig']
        );
    }

    public function testFirstCall() 
    {
        $this->assertEquals(true, $this->Call->FirstCall());
        
        
         $Lead = new LeadModel(
            $this->config,
            11
        );
        $Call = new CallModel(
            $this->config['twilio'], 
            $Lead->UserData, 
            $Lead->SalesPerson, 
            $this->config['DBconfig']
        );
        $this->assertEquals(false, $Call->FirstCall());
    }
}