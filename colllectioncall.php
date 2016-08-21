<?php

//****************define variables*****************************************
//loop through the conversation
$loop_finished = 0;

//start the conversation at scenario ID number 0
$current_scenario = "0";

//default error message if XML cannot be read
$scenario_say = "Error. I could not access the data file needed to continue this conversation.";

//counts how many times the conversation has gone back and forth
$loop_counter = 0;

//**************initialize the speech to text API**************************
//getting ATT Token ID
	set_time_limit (300);
	error_reporting(2047); 
	ini_set("display_errors",1);

	$crl = curl_init();
	
	$headr = array();
	$headr[] = 'Accept: application/json';
	$headr[] = 'Content-Type: application/x-www-form-urlencoded';
	
	curl_setopt($crl, CURLOPT_URL, "https://api.att.com/oauth/token" );
	curl_setopt($crl, CURLOPT_POST, 1);
	curl_setopt($crl, CURLOPT_POSTFIELDS, "client_id=09bf82299ed9375ba89c3bd81ec91dcb&client_secret=1d7504068e5edec6&grant_type=client_credentials&scope=SPEECH,STTC" ); 
	curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
	curl_setopt ($crl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($crl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
	
	$result=curl_exec ($crl);
	$decoded_json = json_decode($result, true);
	$token_id = $decoded_json["access_token"]; //<-- This is the token
	curl_close($crl);
	//finished getting ATT Token ID
	
//***********Read the file which holds details about the conversation and save to arrays************

//***Array Formats***
//array_default_remark[scenario id #]
//array_say[scenario id #]
//array_threshold[scenario id #]
//array_listen_for[scenario id #][prompt #]
//array_redirect_to_scenario[scenario id #][prompt #]
//array_departing_remarks[scenario id #][prompt #]

//array_number_of_prompts[scenario id #]
//array_number of scenarios

$array_default_remark = array();
$array_say = array();
$array_threshold = array();
$array_listen_for = array();
$array_redirect_to_scenario = array();
$array_departing_remarks = array();

$array_number_of_prompts = array();
$number_of_scenarios = 0;

	//reading XML file
	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	
	$xmlurl = 'http://www.mattnutsch.com/development/collection/collection_call_script.xml';

	$xml = file_get_contents($xmlurl, false, $context);
	$xml = simplexml_load_string($xml);

	$prompt_listen_for = array();
	$prompt_redirect_to_scenario = array();
	$prompt_departing_remarks = array();
	unset($prompt_listen_for);
	unset($prompt_redirect_to_scenario);
	unset($prompt_departing_remarks);
	
	$prompt_counter = 0;
	$scenario_counter = 0;

	foreach($xml->scenario as $scenarios) 
	{ 
			$scenario_say = (string)$xml->scenario[$scenario_counter]->say;
			$scenario_say = trim(preg_replace("/\s+/", " ", $scenario_say));
			$array_say[$scenario_counter] = $scenario_say;

			$scenario_default_redirect_to_scenario = (string)$xml->scenario[$scenario_counter]->default_redirect_to_scenario;
			$scenario_default_redirect_to_scenario = trim(preg_replace("/\s+/", " ", $scenario_default_redirect_to_scenario));
			$array_redirect_to_scenario[$scenario_counter] = $scenario_default_redirect_to_scenario;

			$scenario_default_remark = (string)$xml->scenario[$scenario_counter]->default_remark;
			$scenario_default_remark = trim(preg_replace("/\s+/", " ", $scenario_default_remark));
			$array_default_remark[$scenario_counter] = $scenario_default_remark;

			$scenario_threshhold = (string)$xml->scenario[$scenario_counter]->threshhold;
			$scenario_threshhold = trim(preg_replace("/\s+/", " ", $scenario_threshhold));
			$array_threshold[$scenario_counter] = $scenario_threshhold;

			$prompt_array_size = sizeof($xml->scenario[$scenario_counter]->prompt);
			$array_number_of_prompts[$scenario_counter] = $prompt_array_size;
			
			$prompt_counter = 0;

				foreach ($xml->scenario[$scenario_counter]->prompt as $valu)
				{
					$prompt_listen_for[$prompt_counter] = (string)$xml->scenario[$scenario_counter]->prompt[$prompt_counter]->listen_for;
					$array_listen_for[$scenario_counter][$prompt_counter] = $prompt_listen_for[$prompt_counter];
					
					$prompt_redirect_to_scenario[$prompt_counter] = (string)$xml->scenario[$scenario_counter]->prompt[$prompt_counter]->redirect_to_scenario;
					$array_redirect_to_scenario[$scenario_counter][$prompt_counter] = $prompt_redirect_to_scenario[$prompt_counter];
					
					$prompt_departing_remarks[$prompt_counter] = (string)$xml->scenario[$scenario_counter]->prompt[$prompt_counter]->departing_remarks;
					$array_departing_remarks[$scenario_counter][$prompt_counter] = $prompt_departing_remarks[$prompt_counter];
					
					$prompt_counter = $prompt_counter + 1;
				}
		
		$scenario_counter = $scenario_counter + 1;
	}
	
	$number_of_scenarios = $scenario_counter;
	//finished reading XML file


//******************************initiate a call to the user**********************************

/*
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
*/

//**********************The conversation will loop until we tell it to stop*******************
while($loop_finished == 0)
{
	//count another exchange in the conversation
	$loop_counter = $loop_counter + 1;
	
	//this is a safety switch and kills the script after 10 iterations
	if($loop_counter > 10) 
	{
		$loop_finished = 1;
	}
	
	//if the conversation script reaches the end then quit the loop
	if($current_scenario  == "999")
	{
		$loop_finished = 1;
	}

	//usleep(0250000);
	//say("The current scenario is", array("voice" => "veronica"));
	
	//usleep(0250000);
	//say($current_scenario, array("voice" => "veronica"));
	
	//usleep(0250000);
	//say("I will read the X M L file.", array("voice" => "veronica"));
	
	//******************Read the details for the current scenario from memory*********************

	$prompt_listen_for = array();
	$prompt_redirect_to_scenario = array();
	$prompt_departing_remarks = array();
	unset($prompt_listen_for);
	unset($prompt_redirect_to_scenario);
	unset($prompt_departing_remarks);
	$prompt_counter = 0;
	$scenario_counter = 0;
	
	for ($scenario_counter=0; $scenario_counter<$number_of_scenarios; $scenario_counter++)
	{ 

		if($scenario_counter] == $current_scenario)
		{
			$scenario_say = $array_say[$scenario_counter]
			
			$scenario_default_redirect_to_scenario = $array_redirect_to_scenario[$scenario_counter]

			$scenario_default_remark = $array_default_remark[$scenario_counter]

			$scenario_threshhold = $array_default_remark[$scenario_counter]

			$prompt_array_size = $array_number_of_prompts[$scenario_counter]
			$prompt_counter = 0;

				for ($prompt_counter=0; $prompt_counter<$array_number_of_prompts[$scenario_counter]; $prompt_counter++)
				{
					$prompt_listen_for[$prompt_counter] = $array_listen_for[$scenario_counter][$prompt_counter];
					$prompt_redirect_to_scenario[$prompt_counter] = $array_redirect_to_scenario[$scenario_counter][$prompt_counter] ;
					$prompt_departing_remarks[$prompt_counter] = $array_departing_remarks[$scenario_counter][$prompt_counter];
				}
		}
	}
	//finished reading from memory

	//speak to the user
	usleep(0250000);
	say($scenario_say, array("voice" => "veronica"));
	
	//record the user's response
	record(".", array( 
		"beep"=>false, 
		"timeout"=>10, 
		"silenceTimeout"=>3, 
		"maxTime"=>15, 
		"recordURI" => "ftp://voice:voice23!@www.mattnutsch.com/recorded_audio.wav" 
		) 
	); 

	//tell the user that it will take a few moments to reply
	usleep(0250000);
	say("processing...", array("voice" => "veronica"));
	
	//convert the recording with the ATT Speech To Text API
	$crl2 = curl_init();
	
	$headr = array();
	$headr[] = 'Authorization: Bearer ' . $token_id;
	$headr[] = 'Accept: application/json';
	$headr[] = 'Content-Type: audio/wav';
	$headr[] = 'X-SpeechContext: SMS';
	
	curl_setopt($crl2, CURLOPT_URL, "https://api.att.com/speech/v3/speechToText" );
	curl_setopt($crl2, CURLOPT_POST, 1);
	
	$fildata = file_get_contents("http://www.mattnutsch.com/development/recorded_audio.wav");
	curl_setopt($crl2, CURLOPT_POSTFIELDS, $fildata);
	curl_setopt($crl2, CURLOPT_HTTPHEADER,$headr);
	curl_setopt ($crl2, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($crl2, CURLOPT_SSL_VERIFYPEER, 0);	
	curl_setopt($crl2, CURLOPT_RETURNTRANSFER, true);
	
	$result2=curl_exec ($crl2);
	$decoded_json2 = json_decode($result2, true);
	
	if(isset($decoded_json2["Recognition"]["NBest"][0]["ResultText"]))
	{
		$result = (string)trim(preg_replace("/\s+/", " ", $decoded_json2["Recognition"]["NBest"][0]["ResultText"]));
	}
	else 
	{
		$result = "blah blah blah";
		$loop_finished = 1;
	}
	//echo 'Message: ' .$e->getMessage();
	//$confidence_level = $decoded_json2["Recognition"]["NBest"][0]["Confidence"];
	curl_close($crl2);
	//finished converting the recording with ATT Speech To Text
	
	//quit the conversation if the user didn't say anything
	if($result == "")
	{
		$loop_finished = 1;
	}
	
	//usleep(0250000);
	//say("I recognized the following.", array("voice" => "veronica"));

	//usleep(0250000);
	//say($result, array("voice" => "veronica"));
		
	//$result = urldecode($result);
	//$result = urlencode($result);
	
	//********Use the SentenceRecogntion.com API to match the user's response to expected statements*******
	$encoded_result = urldecode($result);
	$encoded_result = urlencode($encoded_result);
	//$encoded_result = urlencode($encoded_result);
	
	$sentence_recognition_api_string = "http://www.sentencerecognition.com/sentencerecognition070313.php?key=560330cbf90cd52c6e5728d9ee5db512";
	$sentence_recognition_api_string = $sentence_recognition_api_string . "&input=" . $encoded_result;
	$sentence_number = 0;
	
	//usleep(0250000);
	//say("I'm building the Sentence Recognition U R L.", array("voice" => "veronica"));
	while  ($sentence_number < ($prompt_array_size - 1))
	//foreach ($prompt_listen_for as $key => $val) 
	{
		$sentence_number = $sentence_number + 1;
		//usleep(0250000);
		//say($sentence_number, array("voice" => "veronica"));
		$text_to_add = (string)trim(preg_replace("/\s+/", " ", $prompt_listen_for[$sentence_number-1]));
		$encoded_text_to_add = urldecode($text_to_add);
		$encoded_text_to_add = urlencode($encoded_text_to_add);
		$sentence_recognition_api_string = $sentence_recognition_api_string . "&sentence" . $sentence_number . "=" . $encoded_text_to_add;
		
		//usleep(0250000);
		//say("I added the following prompt text.", array("voice" => "veronica"));

		//usleep(0250000);
		//say($text_to_add, array("voice" => "veronica"));
		
		//usleep(0250000);
		//say("to prompt number.", array("voice" => "veronica"));
		
		//usleep(0250000);
		//say($sentence_number, array("voice" => "veronica"));
	} 
	
	$result = urldecode($result);
	//read the XML from the Sentence Recognition API
	
	//$sr_api_string_to_say = preg_replace('~.~', '\\0 ', $sentence_recognition_api_string);
	//$sr_api_string_to_say = urldecode($sentence_recognition_api_string);
	
	//usleep(0250000);
	//say("The Sentence Recognition U R L is.", array("voice" => "veronica"));
	
	//usleep(0250000);
	//say($sr_api_string_to_say, array("voice" => "veronica"));
	
	$context2  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	
	$SRurl = $sentence_recognition_api_string;

	$SRxmlstream = file_get_contents($SRurl, false, $context2);
	$SRxml = simplexml_load_string($SRxmlstream);
	//$SRxml = simplexml_load_file($sentence_recognition_api_string);
	
	//usleep(0250000);
	//say("The Sentence Recognition X M L is.", array("voice" => "veronica"));

	//usleep(0250000);
	//say(var_dump($matching_prompt), array("voice" => "veronica"));
	
	$sr_xml = preg_replace('~.~', '\\0 ', var_dump($SRxml));
	
	//usleep(0250000);
	//say("The Sentence Recognition X M L is.", array("voice" => "veronica"));
	
	//usleep(0250000);
	//say($sr_xml, array("voice" => "veronica"));
	
	//get the matching_prompt and matching_prompt_score
	$matching_prompt = 0;
	$matching_prompt_score = 0;
	$matching_prompt = (string)$SRxml->matching_prompt;
	$matching_prompt_score = (string)$SRxml->matching_prompt_score;
	$matching_prompt_score = (string)(trim(preg_replace("/\s+/", " ", $matching_prompt_score)));
	
	//usleep(0250000);
	//say("I matched that to the following.", array("voice" => "veronica"));

	//usleep(0250000);
	//say($matching_prompt, array("voice" => "veronica"));
	
	//usleep(0250000);
	//say("The matching score is.", array("voice" => "veronica"));

	//usleep(0250000);
	//say($matching_prompt_score, array("voice" => "veronica"));
	
	//compare the Sentence Recognition API results against the options
	//picking the new scenario and the departing remarks
	
	if(intval($matching_prompt_score) > intval($scenario_threshhold))
	{
		//usleep(0250000);
		//say("The prompt score is greater than the threshhold", array("voice" => "veronica"));
		
		//$theshhold_status = "The prompt score is greater than the threshhold";
		foreach ($prompt_listen_for as $key => $val) 
		{
			//usleep(0250000);
			//say("comparing " . $matching_prompt . " against " . trim(preg_replace("/\s+/", " ", $prompt_listen_for[$key])), array("voice" => "veronica"));
			
			if($matching_prompt == trim(preg_replace("/\s+/", " ", $prompt_listen_for[$key])))
			{
				//usleep(0250000);
				//say("The matching prompt was found", array("voice" => "veronica"));
				
				$current_scenario = (string)$prompt_redirect_to_scenario[$key];
				$current_scenario = (string)urlencode(trim(preg_replace("/\s+/", " ", $current_scenario)));
				$departing_remarks = (string)$prompt_departing_remarks[$key];
				$departing_remarks = (string)urlencode(trim(preg_replace("/\s+/", " ", $departing_remarks)));
				$srcs = $current_scenario;
				$srdr = urldecode($departing_remarks);
			}
		} 
	}
	else
	{
		//usleep(0250000);
		//say("The prompt score is less than the threshhold", array("voice" => "veronica"));
		
		//$theshhold_status = "The prompt score is less than the threshhold";
		$current_scenario = $scenario_default_redirect_to_scenario;
		$departing_remarks = $scenario_default_remark;
		$srcs = $scenario_default_redirect_to_scenario;
		$srdr = urldecode($scenario_default_remark);
	}
	//finished using the Sentence Recognition API

	//speak the departing remarks to the user if there are any
	usleep(0250000);
	say($srdr, array("voice" => "veronica"));
}
	
usleep(0250000);
say("Goodbye.", array("voice" => "veronica"));

?>
