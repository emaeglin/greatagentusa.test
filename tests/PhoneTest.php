<?php
include_once dirname(__FILE__) . '/../core/init.php';

class PhoneTest extends PHPUnit_Framework_TestCase
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
    
    public function testCanBeInvalid() 
    {
        $this->assertEquals(false, PhoneModel::IsValidPhone("a"));
        $this->assertEquals(false, PhoneModel::IsValidPhone(1));
        $this->assertEquals(false, PhoneModel::IsValidPhone(false));
        $this->assertEquals(false, PhoneModel::IsValidPhone(true));
        $this->assertEquals(false, PhoneModel::IsValidPhone(array(1,1)));
    }
    
    public function testCanBeValid()
    {
        $this->assertEquals(true, PhoneModel::IsValidPhone("+1-541-754-3010"));
        $this->assertEquals(true, PhoneModel::IsValidPhone("1-541-754-3010"));
        $this->assertEquals(true, PhoneModel::IsValidPhone("(541) 754-3010"));
    }
    
    public function testCanBeCompanyPhone()
    {
        $this->assertEquals(true, PhoneModel::IsCompanyPhone($this->config['google'], "+1-800–692–7753"));
    }
    
    public function testCanBeNotCompanyPhone()
    {
        $this->assertEquals(false, PhoneModel::IsCompanyPhone($this->config['google'], "+1-512-619-6498"));
    }
    
    public function testCanBeInvalidTwilioLookup()
    {
        $this->assertEquals(false, PhoneModel::TwilioLookup($this->config['twilio'], "+1-541-754-30"));
        $this->assertEquals(false, PhoneModel::TwilioLookup($this->config['twilio'], ""));
        $this->assertEquals(false, PhoneModel::TwilioLookup($this->config['twilio'], 123));
        $this->assertEquals(false, PhoneModel::TwilioLookup($this->config['twilio'], "kasdasd"));
    }
    
    public function testCanBeValidTwilioLookup()
    {
        $this->assertEquals("+15417543010", PhoneModel::TwilioLookup($this->config['twilio'], "+1-541-754-3010"));
        $this->assertEquals("+15417543010", PhoneModel::TwilioLookup($this->config['twilio'], "1-541-754-3010"));
    }
}