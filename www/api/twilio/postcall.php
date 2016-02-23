<?php
require dirname(__FILE__) . '/../../../core/init.php';

if (!isset($_REQUEST['Digits']) || !isset($_REQUEST['lead_id'])) {
    create_error_voice();
} 


$Digits = $_REQUEST['Digits'];
$LeadID = $_REQUEST['lead_id'];

switch ($Digits) {
    case "0":
        //new_voice(1);
        cretate_conference($LeadID);
        break;
    case "1":
        new_voice(30, $LeadID);
        //set remind 30
        break;
    case "2":
        new_voice(60, $LeadID);
        //set remind 60
        break;
    case "3":
        new_voice("tomorrow", $LeadID);
        //set remind tomorrow
        break;
    default:
        continue_choise($LeadID);
}



function new_voice($x, $LeadID) 
{
    global $google;
    global $twilio;
    global $DBconfig;
    $Lead = new LeadModel(array(
        'google'    => $google,
        'twilio'    => $twilio,
        'DBconfig'  => $DBconfig,
    ), $LeadID);
    
    $Lead->SetNextCallOffset($x, $LeadID);
    
    if ($x == "tomorrow") {
        $phrase = "We will call You tomorrow.";
    } else {
        $phrase = "We will call You in $x minutes.";
    }
    
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    ?>
    <Response>
        <Say>
            Thank You.
            <?php echo $phrase; ?>
        </Say>
    </Response>
    <?php
    exit();
}

function cretate_conference($LeadID)
{
    global $google;
    global $twilio;
    global $DBconfig;
    $Lead = new LeadModel(array(
        'google'    => $google,
        'twilio'    => $twilio,
        'DBconfig'  => $DBconfig,
    ), $LeadID);
    
    $Lead->SetNextCallOffset("never", $LeadID);
    
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    ?>
    <Response>
        <Dial><?php echo $Lead->UserData['Phone']; ?></Dial>
            <Say>The call failed or the remote party hung up. Goodbye.</Say>
        </Response>
    <?php
    exit();
}


function continue_choise($LeadID) 
{
    
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    ?>
    <Response>
        <Gather timeout="1" action="/api/twilio/postcall.php?lead_id=<?php echo $LeadID;?>" method="POST">
            <Say>
                Press 1 to postpone this call 30 minutes. 
            </Say>
        </Gather>

        <Gather timeout="1" action="/api/twilio/postcall.php?lead_id=<?php echo $LeadID;?>" method="POST">
            <Say>
                Press 2 to postpone this call 60 minutes. 
            </Say>
        </Gather>

        <Gather timeout="10" action="/api/twilio/postcall.php?lead_id=<?php echo $LeadID;?>" method="POST">
            <Say>
                Press 3 to get a call back tomorrow morning.
            </Say>
        </Gather>
    </Response>
    <?php
    exit();
}

function create_error_voice() {
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    ?>
    <Response>
        <Say>
            Sorry.
            Something is wrong with service. 
            We will call You as soon as possible.
        </Say>
    </Response>
    <?php
    exit();
}
 