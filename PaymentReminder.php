<?php    

/*
call('+'.$customer_phone_number);

record(".", array( 
    "beep"=>false, 
    "timeout"=>10, 
    "silenceTimeout"=>1, 
    "maxTime"=>15, 
    "recordURI" => "ftp:/mnutsch23:hound.spirit98@ftp.tropo.com/recordings/file.wav" 
    ) 
); 
*/

call('+'.$customer_phone_number, array(
   "timeout" => 10,
   "callerID" => '12539612914',
   "onAnswer" => "answerFCN",
   "onTimeout" => "timeoutFCN",
   "onCallFailure" => "callFailureFCN",
   "onBusy" => "busyFCN",
   "onHangup" => "hangupFCN"
   )
);

function answerFCN($event) {
   _log("Call answered");
}
function timeoutFCN($event) {
   _log("Call timed out");
   $loop_finished = 1;
}
function callFailureFCN($event) {
   _log("Call could not be completed as dialed");
   $loop_finished = 1;
}
function busyFCN($event) {
   _log("The phone number was busy");
   $loop_finished = 1;
}
function hangupFCN($event) {
   _log("The call ended");
   $loop_finished = 1;
}

//wait for someone to answer
record(".", array( 
    "beep"=>false, 
    "timeout"=>10, 
    "silenceTimeout"=>1, 
    "maxTime"=>15, 
    "recordURI" => "ftp:/mnutsch23:hound.spirit98@ftp.tropo.com/recordings/file.wav" 
    ) 
); 

usleep(0250000);
say("Hello", array("voice" => "veronica"));
usleep(0250000);
say("This is the Credit Department of Ferguson Enterprises calling for " . $customer_name . ".", array("voice" => "veronica")); 
usleep(0250000);
say("Your account number " . $account_number . " is past due.", array("voice" => "veronica")); 
usleep(0250000);
say("The total balance due is " . $amount_due . " dollars.", array("voice" => "veronica"));
 usleep(0250000);
say("Please visit us online at w w w dot ferguson online dot com 
or call us at " . $call_back_number . " to pay this balance.", array("voice" => "veronica"));
usleep(0250000);
say("Again, you can visit us online at w w w dot ferguson online dot com or call us at " . $call_back_number . " to pay this balance.", array("voice" => "veronica"));
usleep(0250000);
say("Thank you for choosing Ferguson Enterprises.", array("voice" => "veronica"));
usleep(0250000);
say("Have a nice day.", array("voice" => "veronica")); 

 ?>