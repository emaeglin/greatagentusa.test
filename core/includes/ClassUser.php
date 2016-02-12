<?php

class UserModel {
    
    private $UserData = false;
    private $google = false;
    private $twilio = false;


    public function __construct($config) {
        $this->google   = $config['google'];
        $this->twilio   = $config['twilio'];
        
        $post=array_map('trim',$_POST);
        
        $this->UserData = array (
            'ClientPhone'    => (isset($post['ClientPhone']) && !empty($post['ClientPhone']))    ? PhoneModel::TwilioLookup($this->twilio, $post['ClientPhone'])  : false,
            'ClientName'     => (isset($post['ClientName'])  && !empty($post['ClientName']))     ? $post['ClientName']  : false,
            'ClientEmail'    => (isset($post['ClientEmail']) && !empty($post['ClientEmail']))    ? $post['ClientEmail'] : false,
            'IsCompanyPhone' => (isset($post['ClientPhone']) && !empty($post['ClientPhone']))    ? PhoneModel::IsCompanyPhone($this->google, $post['ClientPhone'])  : false,
        );
    }
    
    public function complete () {
        if ($this->UserData['ClientPhone'] === false ||
            $this->UserData['ClientName']  === false ||
            $this->UserData['ClientEmail'] === false
        ) return false;
        
        return true;
    }
    
    public function Save() {
        echo "<pre>";
        var_dump($this->UserData);
        echo "</pre>";
    }
}