<?php

/*
 * Phone number validation by TwillioLookup
 * Check for company number
 */

class PhoneModel {
    static function TwilioLookup ($twilio, $number) {
        if (empty($number))
            return false;

        $ch = curl_init(sprintf($twilio['LookupUrl'], trim($number)));
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
        curl_setopt($ch, CURLOPT_HTTPAUTH,          CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    2);
        curl_setopt($ch, CURLOPT_USERPWD,           sprintf("%s:%s", $twilio['AccountSID'], $twilio['AuthToken']));
        
        $response = curl_exec($ch);
        $response = json_decode($response);
        if (isset($response->phone_number) && !empty($response->phone_number))
            return $response->phone_number;
        
        return false;
    }
    
    static function IsCompanyPhone ($google, $numer) {
        $url = sprintf($google['SearchApiUrl'],$numer, $_SERVER['REMOTE_ADDR']);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        $response = curl_exec($ch);

        $response = json_decode($response);
        
        if (!isset($response->responseData->results[0]->visibleUrl))
            return false;
        
        $cname = explode(".", $response->responseData->results[0]->visibleUrl);
        $company_name = ($cname[0] == 'www') ? $cname[1] : $cname[0];
        
        
        $url = sprintf($google['MapsApiUrl'], $company_name);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $response = json_decode($response);
        
        
        if (isset($response) && $response->status == 'ZERO_RESULTS')
            return false;
        
        return true;
    }
}