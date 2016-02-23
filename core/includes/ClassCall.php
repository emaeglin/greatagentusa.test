<?php
//3 / 3
require dirname(__FILE__) . '/vendors/twilio/Services/Twilio.php';

class CallModel {
    private $Call = null;
    private $twilio = false;
    
    private $TwilioClient = null;


    private $Lead = false;
    private $SalesPerson = false;
    
    private $CallID = 0;

    private $Db = null;
    
    private $DBconfig = false;

    public function __construct($twilio, $lead, $salesperson, $DBconfig=false)
    {
        $this->DBconfig = $DBconfig;
        if (!isset($lead['LeadID']) || !isset($salesperson['SalesPersonID'])) {
            return false;
        }
        
        $this->Lead = $lead;
        $this->SalesPerson = $salesperson;
        $this->twilio   = $twilio;
        return true;
    }
    
    private function Save($Sid) 
    {
        $this->Db = new DbModel($this->DBconfig);
        $this->Db->PrepareQuery(
            "INSERT INTO `GA_LeadCall` (`LeadID`, `Outcome`,`SalesPersonID`, `CallSid`) " . 
            "VALUES (%d, '%s', %d, '%s');",
            array(
                $this->Lead['LeadID'],
                "First Call",
                $this->SalesPerson['SalesPersonID'],
                $Sid
            )
        );
        
        if (!$result = $this->Db->Query()) {
            $this->error = "Wrong query\n";
            return false;
        }

        $this->CallID = $this->Db->LastInsertId();
        return true;
    }

    public function FirstCall() 
    {

        // Instantiate a new Twilio Rest Client
        $this->TwilioClient = new Services_Twilio(
            $this->twilio['AccountSID'], 
            $this->twilio['AuthToken'], 
            $this->twilio['Version']
        );
        
        try {
            // Initiate a new outbound call
            $this->call = $this->TwilioClient->account->calls->create(
                $this->twilio['ValidatedPhone'], // The number of the phone initiating the call
                $this->SalesPerson['phone'], // The number of the phone receiving call
                $this->twilio['Voices']['FirstCall'] . "?lead_id=" . $this->Lead['LeadID'] . "&Name=" . urlencode($this->Lead['Name'])
            );
            if ($this->Save($this->call->sid)) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}