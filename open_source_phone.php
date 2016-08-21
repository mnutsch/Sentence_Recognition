<?php

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
	
	usleep(0250000);
	say("Hello, what is your favorite pasttime?", array("voice" => "veronica"));
	
	record(".", array( 
		"beep"=>false, 
		"timeout"=>10, 
		"silenceTimeout"=>5, 
		"maxTime"=>15, 
		"recordURI" => "ftp://voice:voice23!@www.mattnutsch.com/recorded_audio.wav" 
		) 
	); 
	
	usleep(0250000);
	say("processing...", array("voice" => "veronica"));
	
	//converting the recording with ATT Speech To Text
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
	
	$encoded_result = urldecode($result);
	$encoded_result = urlencode($encoded_result);
	$result = urldecode($result);
	
	$sentence_recognition_api_string = "http://www.sentencerecognition.com/sentencerecognition070313.php?key=560330cbf90cd52c6e5728d9ee5db512";
	$sentence_recognition_api_string = $sentence_recognition_api_string . "&input=" . $encoded_result;
	$sentence_recognition_api_string = $sentence_recognition_api_string . "&sentence1=sing+a+song&sentence2=walk+on+the+beach&sentence3=play+a+game&sentence4=watch+a+movie";
	
	$context2  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	
	$SRurl = $sentence_recognition_api_string;

	$SRxmlstream = file_get_contents($SRurl, false, $context2);
	$SRxml = simplexml_load_string($SRxmlstream);
	
	$sr_xml = preg_replace('~.~', '\\0 ', var_dump($SRxml));
	
	//get the matching_prompt and matching_prompt_score
	$matching_prompt = 0;
	$matching_prompt_score = 0;
	$matching_prompt = (string)$SRxml->matching_prompt;
	$matching_prompt_score = (string)$SRxml->matching_prompt_score;
	$matching_prompt_score = (string)(trim(preg_replace("/\s+/", " ", $matching_prompt_score)));
	
	usleep(0250000);
	say("I recognized the following text using the A T and T A P I.", array("voice" => "veronica"));

	usleep(0250000);
	say($result, array("voice" => "veronica"));
	
	usleep(0250000);
	say("I matched the meaning of what you said to the following with the Sentence Recognition A P I.", array("voice" => "veronica"));

	usleep(0250000);
	say($matching_prompt, array("voice" => "veronica"));
	
	usleep(0250000);
	say("On a scale of zero to one hundred, the number describing how well your statement fits the meaning of the matched text is.", array("voice" => "veronica"));
	
	usleep(0250000);
	say($matching_prompt_score, array("voice" => "veronica"));
	
	usleep(0500000);
	say("Goodbye.", array("voice" => "veronica"));
	
?>