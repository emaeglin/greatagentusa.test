<?php

class PageModel {
    static function Success($IsCompanyPhone=false) {
        echo "Success<br>";
        if ($IsCompanyPhone) {
            echo "Company Phone";
        } else {
            echo "Not Company Phone";
        }
    }
    
    static function Error() {
        echo "Error";
    }
}