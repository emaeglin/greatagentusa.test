<?php

/*
 * Phone number validation by TwillioLookup
 * Check for company number
 */

class PhoneModel
{
    static function TwilioLookup($twilio, $number) 
    {
        if (empty($number)) {
            return false;
        }

        $ch = curl_init(sprintf($twilio['LookupUrl'], trim($number)));
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
        curl_setopt($ch, CURLOPT_HTTPAUTH,          CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    2);
        curl_setopt($ch, CURLOPT_USERPWD,           sprintf("%s:%s", $twilio['AccountSID'], $twilio['AuthToken']));
        
        $response = curl_exec($ch);
        $response = json_decode($response);
        
        if (isset($response->phone_number) && !empty($response->phone_number)) {
            return $response->phone_number;
        }
        
        return false;
    }
    
    static function IsCompanyPhone($google, $numer)
    {
        $url = sprintf($google['SearchApiUrl'],$numer, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "10.0.0.1");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        $response = curl_exec($ch);

        $response = json_decode($response);
        
        if (!isset($response->responseData->results[0]->visibleUrl)) {
            return false;
        }
        
        $cname = explode(".", $response->responseData->results[0]->visibleUrl);
        $company_name = ($cname[0] == 'www') ? $cname[1] : $cname[0];
        
        $url = sprintf($google['MapsApiUrl'], $company_name);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $response = json_decode($response);
        
        
        if (isset($response) && $response->status == 'ZERO_RESULTS') {
            return false;
        }
        
        return true;
    }
    
    static function IsValidPhone($number)
    {
        if (!$number || !is_string($number)) {
            return false;
        }

        /*
        * Pattern for International or Dialed in the US number
        * International: +1-541-754-3010
        * Dialed in the US: 1-541-754-3010
        */
        $pattern_1 = '/^(\+?)(1{1})-\d{3}-\d{3}-\d{4}$/';

        /*
        * Pattern for Domestic number
        * Domestic: (541) 754-3010
        */
        $pattern_2 = '/^\(\d{3}\)\s\d{3}-\d{4}$/';

        if (preg_match($pattern_1, trim($number)) || preg_match($pattern_2, trim($number))) {
            return true;
        }

        return false;
    }
    
    static function PrepareForInsert($number)
    {
        $split = str_split(str_replace("+","",$number));
        
        if (count($split) == 11) {
            unset($split[0]);
        }
        
        $formatted_number = "";
        $c = 0;
        
        foreach ($split as $v) {
            $formatted_number .= $v;
            $c++;
            if (in_array($c, array(3,6))) {
                $formatted_number .= "-";
            }
        }
        
        return $formatted_number;
    }
}