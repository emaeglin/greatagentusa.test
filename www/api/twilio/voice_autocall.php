<?php

require dirname(__FILE__) . '/../../../core/init.php';

//caller name
$Name = isset($_REQUEST['Name']) ? urldecode($_REQUEST['Name']) : "";
$LeadID = isset($_REQUEST['lead_id']) ? $_REQUEST['lead_id'] : 0;

// now greet the caller
header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

?>
<Response>
    <Say>Hello</Say>
    <Say>A Great Agent lead <?php echo $Name;?> waiting for calling right now.</Say>
    <Gather timeout="5" finishOnKey="*" action="/api/twilio/postcall_autocall.php?lead_id=<?php echo $LeadID; ?>" method="POST">
        <Say>
            Stay on the line or press 0 to get connected now.
        </Say>
    </Gather>
</Response>