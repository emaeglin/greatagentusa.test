<?php
//9 / 4
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

    private $Db = NULL;

    public $Id  = 0;
    
    public $SalesPerson = null;
    
    public $error = "";

    public function __construct($config, $LeadID=0) 
    {
        $this->google   = $config['google'];
        $this->twilio   = $config['twilio'];
        $this->DBconfig = $config['DBconfig'];
        
        $this->Db = new DbModel($this->DBconfig);
        if ($LeadID != 0) {
            $this->GetLead($LeadID);
            return true;
        }
        
        
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
    
    private function GetLead($LeadID) 
    {
        $this->Db->PrepareQuery(
            "SELECT `LeadID`, `Phone`, `Name`, `Email`, `SalesPersonID` " .
            "FROM `GA_Lead` " .
            "WHERE `LeadID` = %d",
            array (
                $LeadID
            )
        );
        $result = $this->Db->Query();
        if (!isset($result->LeadID)) {
            $this->error = "Wrong LeadID\n";
            return false;
        }
        $this->UserData = array (
            'Phone'    => $result->Phone,
            'Name'     => $result->Name,
            'Email'    => $result->Email,
            'LeadID'   => $LeadID,
        );
        $this->Id = $LeadID;
        $this->SalesPersonID = $result->SalesPersonID;
        $this->MLSSourceID = $this->GetMLSSourceID();
        $this->GetSalesPerson();
    }

    public function Complete() 
    {
        
        if ($this->UserData === false ||
            $this->UserData['Phone'] === false ||
            $this->UserData['Name']  === false ||
            $this->UserData['Email'] === false
        ) return false;
        return true;
    }
    
    private function GetMLSSourceID() 
    {
        if (!isset($this->UserData['HouseID'])) {
            return 1;
        }
        //GET MLSSourceID
        $this->Db->PrepareQuery(
            "SELECT `MLSSourceID` " .
            "FROM `House` " .
            "WHERE `HouseID` = %d ",
            array (
                $this->UserData['HouseID']
            )
        );
        $result = $this->Db->Query();
        if (!isset($result->MLSSourceID)) {
            $this->error = "Wrong HouseID\n";
            return false;
        }
        
        return $result->MLSSourceID;
    }
    
    private function GetSalesPerson()
    {
        //GET SalesPersonID
        $this->Db->PrepareQuery(
            "SELECT S.`SalesPersonID`, S.`Name`, S.`Cell` AS phone " .
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
        $result = $this->Db->Query();
        if (isset($result->SalesPersonID)) {
            $this->SalesPerson = (array)$result;
            $this->SalesPersonID = $result->SalesPersonID;
            return true;
        }
        return false;
    }

    public function LeadExists()
    {
        //check did lead with same phone adn email exist
        $this->Db->PrepareQuery(
            "SELECT `LeadID` FROM `GA_Lead` WHERE `Phone` = '%s' AND `Email` = '%s' LIMIT 1;",
            array (
                $this->phone->formatted_number,
                $this->UserData['Email'],
            )
        );
        
        $result = $this->Db->Query();
        
        if (isset($result->LeadID)) {
            $this->Id = $result->LeadID;
            $this->error = sprintf(
                    "Lead with phone number '%s' and email '%s' already exist\n",
                    $this->phone->formatted_number,
                    $this->UserData['Email']
            );
            return true;
        }
        return false;
    }

    public function Save()
    {
        if ($this->LeadExists()) {
            return false;
        }
        
        $this->MLSSourceID = $this->GetMLSSourceID();
        $this->GetSalesPerson();
        
        //Save Lead
        $this->Db->PrepareQuery(
            "INSERT INTO `GA_Lead` (`Name`, `Phone`, `Email`, `Source`, `SalesPersonID`, `BadNumber`) " . 
            "VALUES ('%s', '%s', '%s', '%s', %d, %d)",
            array(
                $this->UserData['Name'],
                $this->phone->formatted_number,
                $this->UserData['Email'],
                $this->Source,
                $this->SalesPersonID,
                (int)$this->UserData['IsCompanyPhone']
            )
        );
        
        if (!$result = $this->Db->Query()) {
            $this->error = "Wrong query\n";
            return false;
        }
        
        $this->Id = $this->Db->LastInsertId();
        $this->UserData['LeadID'] = $this->Id;
        //call if phone OK and SalesPerson exist;
        $Call = new CallModel($this->twilio, $this->UserData, $this->SalesPerson, $this->DBconfig);
        $Call->FirstCall();
        
        return true;
    }
    
    public function SetNextCallOffset($offset = 30, $lid_id=0) 
    {
        
        if ($offset == "tomorrow") {
            $date = date("Y-m-d", time()+86400) . " 09:00:00";
        } elseif ($offset == "never") {
            $date = "0000-00-00 00:00:00";
        } else {
            $date = date('Y-m-d H:i:s', strtotime("+" .intval($offset). " minutes"));
        }
        
        $this->Db->PrepareQuery(
            "UPDATE `GA_Lead` " . 
            "SET `nextCallDue`= '%s' " .
            "WHERE LeadID=%d;",
            array(
                $date,
                $lid_id
            )
        );
        $this->Db->Query();
        
        if (!$result = $this->Db->Query()) {
            $this->error = "Wrong query\n";
            return false;
        }
        
        return true;
        
    }
    
    public function CompleteLead() 
    {
        $this->Db->PrepareQuery(
            "UPDATE `GA_Lead` SET `AutoCallComplete` = 1, `AutoCallLocked` = 0, `nextCallDue` = '' WHERE `LeadID` = %d;",
            array($this->Id)
        );
        $this->Db->Query();
        return true;
    }
}