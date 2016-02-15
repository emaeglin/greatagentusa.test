<?php
include_once dirname(__FILE__) . '/../core/init.php';

class PhoneTest extends PHPUnit_Framework_TestCase
{
    
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
        global $google;
        $this->assertEquals(true, PhoneModel::IsCompanyPhone($google, "+1-800–692–7753"));
    }
    
    public function testCanBeNotCompanyPhone()
    {
        global $google;
        $this->assertEquals(false, PhoneModel::IsCompanyPhone($google, "+1-512-619-6498"));
    }
    
    public function testCanBeInvalidTwilioLookup()
    {
        global $twilio;
        $this->assertEquals(false, PhoneModel::TwilioLookup($twilio, "+1-541-754-30"));
        $this->assertEquals(false, PhoneModel::TwilioLookup($twilio, ""));
        $this->assertEquals(false, PhoneModel::TwilioLookup($twilio, 123));
        $this->assertEquals(false, PhoneModel::TwilioLookup($twilio, "kasdasd"));
    }
    
    public function testCanBeValidTwilioLookup()
    {
        global $twilio;
        $this->assertEquals("+15417543010", PhoneModel::TwilioLookup($twilio, "+1-541-754-3010"));
        $this->assertEquals("+15417543010", PhoneModel::TwilioLookup($twilio, "1-541-754-3010"));
    }
}