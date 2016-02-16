<?php

class LeadModel 
{
    public  $UserData = false;
    
    private $SalesPersonID = 1;
    private $Source = "emaeglin test";
    private $MLSSourceID = 1;

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
        
        if (isset($config['newLeadSource'])) {
            $this->Source = $config['newLeadSource'];
        }
        
        $this->UserData = array (
            'Phone'    => $this->phone->number,
            'Name'     => (isset($post['ClientName'])  && !empty($post['ClientName']))    ? $post['ClientName']  : false,
            'Email'    => (isset($post['ClientEmail']) && !empty($post['ClientEmail']))   ? $post['ClientEmail'] : false,
            'HouseID'  => (isset($post['HouseID']) && !empty($post['HouseID']))   ? $post['HouseID'] : false,
            'IsCompanyPhone' => $this->phone->IsCompanyPhone()
        );
    }
    
    public function Complete() 
    {
        if ($this->UserData['Phone'] === false ||
            $this->UserData['Name']  === false ||
            $this->UserData['Email'] === false
        ) return false;
        
        return true;
    }
    
    public function Save()
    {
        $Db = new DbModel($this->DBconfig);
        
        //GET MLSSourceID
        $Db->PrepareQuery(
            "SELECT `MLSSourceID` " .
            "FROM `House` " .
            "WHERE `HouseID` = %d ",
            array (
                $this->UserData['HouseID']
            )
        );
        $result = $Db->Query();
        if (!isset($result->MLSSourceID)) {
            $this->error = "Wrong HouseID\n";
            return false;
        }
        $this->MLSSourceID = $result->MLSSourceID;
        
        //GET SalesPersonID
        $Db->PrepareQuery(
            "SELECT S.`SalesPersonID` " .
            "FROM `GA_User` U " .
            "LEFT JOIN `GA_SalesPerson` S " .
            "ON S.`UserID` = U.`UserID` " .
            "WHERE " .
            "U.`HasCancelled` = 0 AND " .
            "U.`MLSSourceIDs` LIKE '%%%d%%' AND " .
            "S.`ReceivePhoneAlerts` = 1 " .
            "ORDER BY Rand() " .
            "LIMIT 1; ",
            array (
                $this->MLSSourceID,
            )
        );
        $result = $Db->Query();
        if (isset($result->SalesPersonID)) {
            $this->SalesPersonID = intval($result->SalesPersonID);
        }
        
        //check did lead with same phone adn email exist
        $Db->PrepareQuery(
            "SELECT `LeadID` FROM `GA_Lead` WHERE `Phone` = '%s' AND `Email` = '%s' LIMIT 1;",
            array (
                $this->phone->formatted_number,
                $this->UserData['Email'],
            )
        );
        
        $result = $Db->Query();
        
        if (isset($result->LeadID)) {
            $this->error = sprintf(
                    "Lead with phone number '%s' and email '%s' already exist\n",
                    $this->phone->formatted_number,
                    $this->UserData['Email']
            );
            return false;
        }
        
        //Save Lead
        $Db->PrepareQuery(
            "INSERT INTO `GA_Lead` (`Name`, `Phone`, `Email`, `Source`, `SalesPersonID`) " . 
            "VALUES ('%s', '%s', '%s', '%s', %d)",
            array(
                $this->UserData['Name'],
                $this->phone->formatted_number,
                $this->UserData['Email'],
                $this->Source,
                $this->SalesPersonID
            )
        );
        
        if (!$result = $Db->Query()) {
            $this->error = "Wrong query\n";
            return false;
        }
        
        $this->Id = $Db->LastInsertId();
        
        return true;
    }
}