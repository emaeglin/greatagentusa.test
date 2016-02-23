<?php
//3
include_once dirname(__FILE__) . '/../core/init.php';

class PhoneTest extends PHPUnit_Framework_TestCase
{
    private $config = array();
    
    public $phone = NULL;


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
        
        $this->phone = new PhoneModel($this->config, "");
    }
    
    public function testCanBeInvalid() 
    {
        $this->phone->number = "a";
        $this->assertEquals(false, $this->phone->IsValidPhone());
        $this->phone->number = 1;
        $this->assertEquals(false, $this->phone->IsValidPhone());
        $this->phone->number = false;
        $this->assertEquals(false, $this->phone->IsValidPhone());
        $this->phone->number = true;
        $this->assertEquals(false, $this->phone->IsValidPhone());
        $this->phone->number = array(1,1);
        $this->assertEquals(false, $this->phone->IsValidPhone());
    }
    
    public function testCanBeValid()
    {
        $this->phone->number = "+1-541-754-3010";
        $this->assertEquals(true, $this->phone->IsValidPhone());
        $this->phone->number = "1-541-754-3010";
        $this->assertEquals(true, $this->phone->IsValidPhone());
        $this->phone->number = "(541) 754-3010";
        $this->assertEquals(true, $this->phone->IsValidPhone());
        
        $this->phone->number = "+15417543010";
        $this->assertEquals(true, $this->phone->IsValidPhone());
        
        
        $this->phone->number = "5126196498";
        $this->assertEquals(true, $this->phone->IsValidPhone());
        $this->phone->number = "512-619-6498";
        $this->assertEquals(true, $this->phone->IsValidPhone());
        $this->phone->number = "(512) 619 6498";
        $this->assertEquals(true, $this->phone->IsValidPhone());
    }
    
    public function testCanBeCompanyPhone()
    {
        $this->phone->number = "+1-800–692–7753";
        $this->assertEquals(true, $this->phone->IsCompanyPhone());
    }
    
    public function testCanBeNotCompanyPhone()
    {
        $this->phone->number = "+1-512-619-6498";
        $this->assertEquals(false, $this->phone->IsCompanyPhone());
    }
    
    public function testCanBeInvalidTwilioLookup()
    {
        $this->phone->number = "+1-541-754-30";
        $this->assertEquals(false, $this->phone->TwilioLookup());
        $this->phone->number = "";
        $this->assertEquals(false, $this->phone->TwilioLookup());
        $this->phone->number = 123;
        $this->assertEquals(false, $this->phone->TwilioLookup());
        $this->phone->number = "kasdasd";
        $this->assertEquals(false, $this->phone->TwilioLookup());
    }
    
    public function testCanBeValidTwilioLookup()
    {
        $this->phone->number = "+1-541-754-3010";
        $this->assertEquals(true, $this->phone->TwilioLookup());
        $this->assertEquals("+15417543010", $this->phone->number);
        
        $this->phone->number = "1-541-754-3010";
        $this->assertEquals(true, $this->phone->TwilioLookup());
        $this->assertEquals("+15417543010", $this->phone->number);
    }
}