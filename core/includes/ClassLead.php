<?php

class LeadModel 
{
    public  $UserData = false;
    private $google = false;
    private $twilio = false;
    private $DBconfig = false;
    
    private $phone = NULL;


    public $Id  = 0;

    public $error = "";

    public function __construct($config) 
    {
        $this->google   = $config['google'];
        $this->twilio   = $config['twilio'];
        $this->DBconfig = $config['DBconfig'];
        
        $post=array_map('trim',$_POST);
        
        if (isset($post['ClientPhone']) && !empty($post['ClientPhone'])) {
            $this->phone = new PhoneModel($config, $post['ClientPhone']);
            $this->phone->Validate();
        } else {
            return false;
        }
        
        $this->UserData = array (
            'ClientPhone'    => $this->phone->number,
            'ClientName'     => (isset($post['ClientName'])  && !empty($post['ClientName']))    ? $post['ClientName']  : false,
            'ClientEmail'    => (isset($post['ClientEmail']) && !empty($post['ClientEmail']))   ? $post['ClientEmail'] : false,
            'IsCompanyPhone' => $this->phone->IsCompanyPhone()
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
            array (
                $this->phone->formatted_number
                )
        );
        
        $result = $Db->Query();
        
        if (isset($result->LeadID)) {
            $this->error = sprintf(
                    "User with phone number %s already exist",
                    $this->phone->formatted_number
            );
            return false;
        }
        
        
        $Db->PrepareQuery(
                "INSERT INTO `GA_Lead` (`Name`, `Phone`, `Email`) VALUES ('%s','%s', '%s');",
                array(
                    $this->UserData['ClientName'],
                    $this->phone->formatted_number,
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