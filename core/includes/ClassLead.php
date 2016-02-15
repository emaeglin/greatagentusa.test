<?php

class LeadModel 
{
    public  $UserData = false;
    private $google = false;
    private $twilio = false;
    private $DBconfig = false;
    
    public $Id  = 0;

    public $error = "";

    public function __construct($config) 
    {
        $this->google   = $config['google'];
        $this->twilio   = $config['twilio'];
        $this->DBconfig = $config['DBconfig'];
        
        $post=array_map('trim',$_POST);
        
        $this->UserData = array (
            'ClientPhone'    => (isset($post['ClientPhone']) && PhoneModel::IsValidPhone($post['ClientPhone'])) ? PhoneModel::TwilioLookup($this->twilio, $post['ClientPhone'])  : false,
            'ClientName'     => (isset($post['ClientName'])  && !empty($post['ClientName']))     ? $post['ClientName']  : false,
            'ClientEmail'    => (isset($post['ClientEmail']) && !empty($post['ClientEmail']))    ? $post['ClientEmail'] : false,
            'IsCompanyPhone' => (isset($post['ClientPhone']) && !empty($post['ClientPhone']))    ? PhoneModel::IsCompanyPhone($this->google, $post['ClientPhone'])  : false,
        );
    }
    
    public function Complete() 
    {
        if ($this->UserData['ClientPhone'] === false ||
            $this->UserData['ClientName']  === false ||
            $this->UserData['ClientEmail'] === false
        ) return false;
        
        return true;
    }
    
    public function Save()
    {
        $Db = new DbModel($this->DBconfig);
        
        //check did user exist
        $Db->PrepareQuery(
            "SELECT `LeadID` FROM `GA_Lead` WHERE `Phone` = '%s' LIMIT 1;",
            array(
                PhoneModel::PrepareForInsert($this->UserData['ClientPhone'])
                )
        );
        
        $result = $Db->Query();
        
        if (isset($result->LeadID)) {
            $this->error = sprintf(
                    "User with phone number %s already exist",
                    PhoneModel::PrepareForInsert($this->UserData['ClientPhone'])
            );
            return false;
        }
        
        
        $Db->PrepareQuery(
                "INSERT INTO `GA_Lead` (`Name`, `Phone`, `Email`) VALUES ('%s','%s', '%s');",
                array(
                    $this->UserData['ClientName'],
                    PhoneModel::PrepareForInsert($this->UserData['ClientPhone']),
                    $this->UserData['ClientEmail']
                )
        );
        
        if (!$result = $Db->Query()) {
            $this->error = "Wrong query";
            return false;
        }
        
        $this->Id = $Db->LastInsertId();
        
        return true;
    }
}