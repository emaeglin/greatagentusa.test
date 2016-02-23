<?php
//1
/*
 * Need to be rewritten
 * !!!!!!
 */
include_once dirname(__FILE__) . '/../core/init.php';

class DbTest extends PHPUnit_Framework_TestCase
{
    private $config = array();
    private $Db = NULL;

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
        
        $this->Db = new DbModel($DBconfig);
    }
   
    
    public function getConnection()
    {   
    }
    
    public function getDataSet()
    {
    }
    
    public function testConnection()
    {
        
        $config1 = $this->config['DBconfig'];
        $Db1 = new DbModel($config1);
        $this->assertEquals(true, $Db1->CheckConnection());
        
        
        $config2 = $this->config['DBconfig'];
        $config2['username'] = "test1";
        @$Db2 = new DbModel($config2);
        $this->assertEquals(false, $Db2->CheckConnection());
        
    }
}