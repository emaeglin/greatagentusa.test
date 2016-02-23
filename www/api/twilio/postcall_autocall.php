<?php
require dirname(__FILE__) . '/../../../core/init.php';

if (!isset($_REQUEST['lead_id'])) {
    create_error_voice();
} 


$LeadID = $_REQUEST['lead_id'];

$Lead = new LeadModel(array(
    'google'    => $google,
    'twilio'    => $twilio,
    'DBconfig'  => $DBconfig,
), $LeadID);


header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Dial action="/api/twilio/callback.php?lead_id=<?php echo $LeadID; ?>" method="POST"><?php echo $Lead->UserData['Phone']?></Dial>
        <Say>The call failed or the remote party hung up. Goodbye.</Say>
    </Response>

<?php
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
 