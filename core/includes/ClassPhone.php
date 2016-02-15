<?php

/*
 * Phone number validation by TwillioLookup
 * Check for company number
 */

class PhoneModel
{
    public $number = "";
    public $formatted_number = "";
    
    private $google = false;
    private $twilio = false;
    
    public $error = "";
    
    public function __construct($config, $number = "") 
    {
        $this->google   = $config['google'];
        $this->twilio   = $config['twilio'];

        $this->number = $number;
        $this->PrepareForInsert();
    }
    
    public function Validate()
    {
        if (!$this->IsValidPhone()) {
            $this->number = false;
            return false;
        }
        $this->TwilioLookup();
    }

    public function TwilioLookup() 
    {
        if (empty($this->number)) {
            return false;
        }

        $ch = curl_init(sprintf($this->twilio['LookupUrl'], trim($this->number)));
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
        curl_setopt($ch, CURLOPT_HTTPAUTH,          CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    2);
        curl_setopt($ch, CURLOPT_USERPWD,           sprintf("%s:%s", $this->twilio['AccountSID'], $this->twilio['AuthToken']));
        
        $response = curl_exec($ch);
        $response = json_decode($response);
        
        if (isset($response->phone_number) && !empty($response->phone_number)) {
            $this->number = $response->phone_number;
            return true;
        }
        
        return false;
    }
    
    public function IsCompanyPhone()
    {
        $url = sprintf($this->google['SearchApiUrl'], $this->number, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "10.0.0.1");

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
        
        $url = sprintf($this->google['MapsApiUrl'], $company_name);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $response = json_decode($response);
        
        
        if (isset($response) && $response->status == 'ZERO_RESULTS') {
            return false;
        }
        
        return true;
    }
    
    public function IsValidPhone()
    {
        if (!$this->number || !is_string($this->number)) {
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

        if (preg_match($pattern_1, trim($this->number)) || preg_match($pattern_2, trim($this->number))) {
            return true;
        }

        return false;
    }
    
    public function PrepareForInsert()
    {
        $split = str_split(str_replace(array("+","-"),"",$this->number));
        
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
        
        $this->formatted_number = $formatted_number;
        return true;
    }
}