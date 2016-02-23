<?php
//7 / 0
class LeadsModel 
{
    private $Db = NULL;
    
    private $google = false;
    private $twilio = false;
    private $DBconfig = false;
    
    public $items = NULL;

    public $error = "";

    public function __construct($config) 
    {
        $this->google   = $config['google'];
        $this->twilio   = $config['twilio'];
        $this->DBconfig = $config['DBconfig'];
        
        $this->Db = new DbModel($this->DBconfig);
    }
    
    public function GetLeadsForCalls()
    {
        $this->Db->PrepareQuery(
            "SELECT	L.`LeadID`, L.`Phone` AS LeadPhone, L.`Name` AS LeadName, L.`Email` AS LeadEmail, \n" .
            "S.`SalesPersonID`, S.`Cell` AS SalesPersonPhone, S.`Name` as SalesPersonName, S.`Email` as SalesPersonEmail \n" .
            "FROM `GA_Lead` L \n" .
            "JOIN `GA_SalesPerson` S ON S.`SalesPersonID` = L.`SalesPersonID` \n" .
            "WHERE L.`AutoCallComplete` = 0 \n" .
            "AND L.`BadNumber` = 0 \n" .
            "AND L.`AutoCallLocked` = 0 \n" .
            "AND L.`nextCallDue` BETWEEN '2016-01-01 00:00:00' AND '%s' \n" .
            "AND S.`SalesPersonID` NOT IN ( \n" .
                    "SELECT DISTINCT `SalesPersonID` \n" .
                    "FROM `GA_Lead` \n" .
                    "WHERE `AutoCallLocked` = 1) \n" .
            "GROUP BY S.`SalesPersonID`;\n",
             array(date('Y-m-d H:i:s'))
        );
        
        $result = $this->Db->Query();
        if (is_null($result)) {
            return false;
        }
        if (!is_array($result)) {
            $this->items[] = $result;
        } else {
            $this->items = $result;
        }
        
        return true;
    }
    
    private function LockLead($LeadID) 
    {
        $this->Db->PrepareQuery(
            "UPDATE `GA_Lead` SET `AutoCallLocked` = 1 WHERE `LeadID` = %d;",
            array($LeadID)
        );
        $this->Db->Query();
        return true;
    }
    
    private function UnlockLead($LeadID) 
    {
        $this->Db->PrepareQuery(
            "UPDATE `GA_Lead` SET `AutoCallLocked` = 0 WHERE `LeadID` = %d;",
            array($LeadID)
        );
        $this->Db->Query();
        return true;
    }
    
    public function StartAutoCalls()
    {
        foreach ($this->items as $Lead) {
            $this->LockLead($Lead->LeadID);
            $this->StartAutoCall($Lead);
        }
    }
    
    private function StartAutoCall($Lead) 
    {
        $this->TwilioClient = new Services_Twilio(
            $this->twilio['AccountSID'], 
            $this->twilio['AuthToken'], 
            $this->twilio['Version']
        );
        
        try {
            // Initiate a new outbound call
            $this->call = $this->TwilioClient->account->calls->create(
                $this->twilio['ValidatedPhone'], // The number of the phone initiating the call
                $Lead->SalesPersonPhone, //SalesPersonPhone
                $this->twilio['Voices']['AutoCall'] . "?lead_id=" . $Lead->LeadID . "&Name=" . urlencode($Lead->LeadName)
            );
            $this->SaveCall($this->call->sid, $Lead);
        } catch (Exception $e) {
            //echo 'Error: ' . $e->getMessage();
        }
    }
    
    private function SaveCall($Sid, $Lead) 
    {
        $this->Db = new DbModel($this->DBconfig);
        $this->Db->PrepareQuery(
            "INSERT INTO `GA_LeadCall` (`LeadID`, `Outcome`,`SalesPersonID`, `CallSid`) " . 
            "VALUES (%d, '%s', %d, '%s');",
            array(
                $Lead->LeadID,
                "Second Call",
                $Lead->SalesPersonID,
                $Sid
            )
        );
        
        if (!$result = $this->Db->Query()) {
            $this->error = "Wrong query\n";
            return false;
        }

        return true;
    }

}