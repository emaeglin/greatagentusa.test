<?php
//3
include_once dirname(__FILE__) . '/../core/init.php';

class PageTest extends PHPUnit_Framework_TestCase
{
    
    public function testSuccess() 
    {
        PageModel::Success();
        $this->expectOutputString("Success<br>");
    }
    
    public function testError()
    {
        PageModel::Error();
        $this->expectOutputString("Error");
        
        PageModel::Error("1213");
        $this->expectOutputString("ErrorError<br><br>1213");
    }
    
}