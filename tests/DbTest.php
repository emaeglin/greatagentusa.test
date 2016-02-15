<?php
include_once dirname(__FILE__) . '/../core/init.php';

class DbTest extends PHPUnit_Extensions_Database_TestCase
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
   
    public function getConnection()
    {
        $pdo = new PDO('sqlite::memory:');
        return $this->createDefaultDBConnection($pdo, ':memory:');
    }
    
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/data-seed.xml');
    }
}