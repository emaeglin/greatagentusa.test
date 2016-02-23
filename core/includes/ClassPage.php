<?php
//2 / 0
class PageModel
{
    static function Success($object=false)
    {
        echo "Success<br>";
    }
    
    static function Error($error = false)
    {
        echo "Error";
        if ($error) {
            echo "<br><br>" . $error;
        }
    }
}