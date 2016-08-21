<?php

function check_input($value)
{
// Stripslashes
/*
if (get_magic_quotes_gpc())
  {
  $value = stripslashes($value);
  }
// Quote if not a number
if (!is_numeric($value))
  {
  $value = "'" . mysql_real_escape_string($value) . "'";
  }
  */
  $value = str_replace("'", "", $value);
return $value;
}

function validate_key ($user_key)
{
	// Create connection
	$con=mysqli_connect("localhost","root","password","sentreco_api");

	// Check connection
	if (mysqli_connect_errno($con))
  	{
  		echo "Failed to connect to database: " . mysqli_connect_error();
  	}
  
	$key_exists = false;
        
	$result = mysqli_query($con,"SELECT * FROM api_keys WHERE api_key='$user_key'");

	while($row = mysqli_fetch_array($result))
  	{
  		$key_exists = true;
   }
   
   mysqli_close($con);

	if ($key_exists == false)
	{
   	return false;
	}  
	else
	{
		return true;
	}
		
}

function Deconjugate($raw_input)
{
	$word_array = explode(" ", $raw_input);
	//var_dump($word_array);
	
	$array_length = count($word_array);
		
	$iterate_count = 0;
	
	$new_string = "";
	
	while($iterate_count < $array_length)
	{
		$new_word = $word_array[$iterate_count];
		switch ($word_array[$iterate_count])
		{
			case "im":
  				$new_word = "i am";
  				break;
			case "ive":
  				$new_word = "i have";
  				break;
			case "id":
  				$new_word = "i would";
  				break;
  			case "isnt":
  				$new_word = "is not";
  				break;
  			case "arent":
  				$new_word = "are not";
  				break;
  			case "wasnt":
  				$new_word = "was not";
  				break;
  			case "werent":
  				$new_word = "were not";
  				break;
  			case "havent":
  				$new_word = "have not";
  				break;
  			case "hasnt":
  				$new_word = "has not";
  				break;
  			case "hadnt":
  				$new_word = "had not";
  				break;
  			case "wont":
  				$new_word = "will not";
  				break;
  			case "dont":
  				$new_word = "do not";
  				break;
  			case "doesnt":
  				$new_word = "does not";
  				break;
  			case "didnt":
  				$new_word = "did not";
  				break;
  			case "cant":
  				$new_word = "can not";
  				break;
  			case "couldnt":
  				$new_word = "could not";
  				break;
  			case "shouldnt":
  				$new_word = "should not";
  				break;
  			case "mightnt":
  				$new_word = "might not";
  				break;
  			case "mustnt":
  				$new_word = "must not";
  				break;
  			case "wouldve":
  				$new_word = "would have";
  				break;
  			case "shouldve":
  				$new_word = "should have";
  				break;
  			case "couldve":
  				$new_word = "could have";
  				break;
  			case "mightve":
  				$new_word = "might have";
  				break;
  			case "mustve":
  				$new_word = "must have";
  				break;
  			case "oclock":
  				$new_word = "of the clock";
  				break;
  			case "maam":
  				$new_word = "madam";
  				break;
  			case "twas":
  				$new_word = "it was";
  				break;
  			case "itll":
  				$new_word = "it will";
  				break;
  			case "shes":
  				$new_word = "she is";
  				break;
  			case "hes":
  				$new_word = "he is";
  				break;
  			case "youve":
  				$new_word = "you have";
  				break;
  			case "hows":
  				$new_word = "how is";
  				break;
  			case "whens":
  				$new_word = "when is";
  				break;
  			case "youre":
  				$new_word = "you are";
  				break;
  			case "youd":
  				$new_word = "you would";
  				break;
  			case "its":
  				$new_word = "it is";
  				break;
  			case "weve":
  				$new_word = "we have";
  				break;
  			case "whos":
  				$new_word = "who is";
  				break;
  			case "wholl":
  				$new_word = "who will";
  				break;
  			case "whod":
  				$new_word = "who would";
  				break;
  			case "hows":
  				$new_word = "how is";
  				break;
  			case "howll":
  				$new_word = "how will";
  				break;
  			case "howd":
  				$new_word = "how would";
  				break;
  			case "whats":
  				$new_word = "what is";
  				break;
			default:
  				$new_word = $new_word;
		}
		$new_string = $new_string . " " . $new_word;
		$iterate_count = $iterate_count + 1;
	}
	$new_string = ltrim($new_string);
	
	return($new_string); 
}

function ProcessInput($user_input, $the_bot_id, $scenario_id) 
{
	//echo "User Input: " . $user_input . "<br>";
	//echo "Bot ID: " . $the_bot_id . "<br>";
	//echo "Scenario ID: " . $scenario_id . "<br>";
	
	$start_time = time();
	
	header('Content-type: text/xml');
	
	$xml_output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"; 
	
	$xml_output .= "<results>\n"; 
	
	//process text
	
	//-convert text to lowercase
	$user_input_lower = strtolower($user_input);
	//echo "Lowercase User Input: " . $user_input_lower . "<br>";
	
	//remove commas and periods
	$user_input_no_periods = str_replace(array('.', ',','!','?'), '' , $user_input_lower);
	//echo "Lowercase User Input No Periods or Commas: " . $user_input_no_periods . "<br>";
	
	//-deconjugate contractions
	$user_input_deconjugated = Deconjugate($user_input_no_periods);
	//echo "Lowercase User Input No Periods or Commas Deconjugate: " . $user_input_deconjugated . "<br>";
	
	//-remove punctuation
	$user_input_lower_alpha = preg_replace("/[^A-Za-z0-9 ]/", '', $user_input_deconjugated);
	//echo "Lowercase User Input Alphanumeric Only: " . $user_input_lower_alpha . "<br>";
	
	$result = $user_input_lower_alpha;
	
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
$array_say_array = array();
$array_threshold = array();
$array_listen_for = array();
$array_redirect_to_scenario = array();
$array_departing_remarks = array();

$array_number_of_prompts = array();
$number_of_scenarios = 0;

	//reading XML file
	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	
	$xmlurl = 'http://www.sentencerecognition.com/sentreco2_bot-' . $the_bot_id . '.xml';

	//echo "xmlurl is " . $xmlurl . "<br>";

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
			//$scenario_say = (string)$xml->scenario[$scenario_counter]->say;
			//$scenario_say = trim(preg_replace("/\s+/", " ", $scenario_say));
			//$array_say[$scenario_counter] = $scenario_say;

			$scenario_default_redirect_to_scenario = (string)$xml->scenario[$scenario_counter]->default_redirect_to_scenario;
			$scenario_default_redirect_to_scenario = trim(preg_replace("/\s+/", " ", $scenario_default_redirect_to_scenario));
			$array_default_redirect_to_scenario[$scenario_counter] = $scenario_default_redirect_to_scenario;

			$scenario_default_remark = (string)$xml->scenario[$scenario_counter]->default_remark;
			$scenario_default_remark = trim(preg_replace("/\s+/", " ", $scenario_default_remark));
			$array_default_remark[$scenario_counter] = $scenario_default_remark;

			$scenario_threshhold = (string)$xml->scenario[$scenario_counter]->threshhold;
			$scenario_threshhold = trim(preg_replace("/\s+/", " ", $scenario_threshhold));
			$array_threshold[$scenario_counter] = $scenario_threshhold;
			//echo "temp: array threshhold is " . $array_threshold[$scenario_counter] . "<br>";

			$prompt_array_size = sizeof($xml->scenario[$scenario_counter]->prompt);
			$array_number_of_prompts[$scenario_counter] = $prompt_array_size;
			
			$say_counter = 0;
			
				foreach ($xml->scenario[$scenario_counter]->say as $valu)
				{
					$scenario_say = (string)$xml->scenario[$scenario_counter]->say[$say_counter];
					$scenario_say = trim(preg_replace("/\s+/", " ", $scenario_say));
					
					$array_say_array[$scenario_counter][$say_counter] = $scenario_say;
					//$xml_output = $xml_output . "<test_say>" . $array_say_array[$scenario_counter][$say_counter] . " " . $scenario_counter . " " . $say_counter . "</test_say>\n";
					$say_counter = $say_counter + 1;
				}
			
			$prompt_counter = 0;

				foreach ($xml->scenario[$scenario_counter]->prompt as $valu)
				{
					$prompt_listen_for[$prompt_counter] = (string)$xml->scenario[$scenario_counter]->prompt[$prompt_counter]->listen_for;
					$array_listen_for[$scenario_counter][$prompt_counter] = $prompt_listen_for[$prompt_counter];
					
					$prompt_redirect_to_scenario[$prompt_counter] = (string)$xml->scenario[$scenario_counter]->prompt[$prompt_counter]->redirect_to_scenario;
					$array_redirect_to_scenario[$scenario_counter][$prompt_counter] = $prompt_redirect_to_scenario[$prompt_counter];
				//echo "temp: redirect to scenario is " . $array_redirect_to_scenario[$scenario_counter][$prompt_counter] . "; scenario counter is " . $scenario_counter . ";prompt count is " . $prompt_counter . "<br>";
					
					$prompt_departing_remarks[$prompt_counter] = (string)$xml->scenario[$scenario_counter]->prompt[$prompt_counter]->departing_remarks;
					$array_departing_remarks[$scenario_counter][$prompt_counter] = $prompt_departing_remarks[$prompt_counter];
					//echo "temp: departing remarks read is " . $array_departing_remarks[$scenario_counter][$prompt_counter] . "<br>";
					$prompt_counter = $prompt_counter + 1;
				}
		
		$scenario_counter = $scenario_counter + 1;
	}
	
	$number_of_scenarios = $scenario_counter;
	//finished reading XML file
	
	//echo "Number of scenarios is " . $number_of_scenarios . "<br>";
	
	
	$current_scenario  = $scenario_id;
	//echo "current scenarios is " . $current_scenario . "<br>";
		
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

		//echo "scenario counter is " . $scenario_counter . "<br>";
		
		if($scenario_counter == $current_scenario)
		{
		
			//$scenario_say = $array_say[$scenario_counter];
			
			$scenario_default_redirect_to_scenario = $array_redirect_to_scenario[$scenario_counter];

			$scenario_default_remark = $array_default_remark[$scenario_counter];

			$scenario_threshhold = $array_threshold[$scenario_counter];
			//echo "temp: scenario threshhold is " . $scenario_threshhold . "<br>";

			$prompt_array_size = $array_number_of_prompts[$scenario_counter];
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
	
		
	
	//if the current scenario is 998, then say the text and move to scenario 0
	if($current_scenario == 998)
	{
	
		$xml_output .= "<departing_remarks></departing_remarks>\n"; 
		$xml_output .= "<say>" . $array_say_array[0][0] . "</say>\n"; 
		$xml_output .= "<new_scenario>0</new_scenario>\n"; 

		//echo "The departing remarks are ". "" . "<br>";
		//echo "The new scenario is " . 0 . "<br>";
		//echo "The response text is " . $array_say[0] . "<br>";
	
	}
	else
	{
	
	//********Use the SentenceRecogntion.com API to match the user's response to expected statements*******
	$encoded_result = urldecode($result);
	$encoded_result = urlencode($encoded_result);
	//$encoded_result = urlencode($encoded_result);
	
	$sentence_recognition_api_string = "http://www.sentencerecognition.com/sentencerecognition.php?key=232323";
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
	
	//$sr_xml = preg_replace('~.~', '\\0 ', var_dump($SRxml));
	
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
	
	//echo "the matching score was " . $matching_prompt_score . "<br>";
	//echo "the scenario threshold was " . $array_threshold[$current_scenario] . "<br>";
	
	//compare the Sentence Recognition API results against the options
	//picking the new scenario and the departing remarks
	
	if(intval($matching_prompt_score) > intval($array_threshold[$current_scenario]))
	{
		//usleep(0250000);
		//echo "The prompt score is greater than the threshhold<br>";
		//echo "the matching prompt was " . $matching_prompt . "<br>";
		//$theshhold_status = "The prompt score is greater than the threshhold";
		foreach ($prompt_listen_for as $key => $val) 
		{
			//usleep(0250000);
			//say("comparing " . $matching_prompt . " against " . trim(preg_replace("/\s+/", " ", $prompt_listen_for[$key])), array("voice" => "veronica"));
			//echo "comparing *" . $matching_prompt . "* against *" . trim(preg_replace("/\s+/", " ", $prompt_listen_for[$key])) .  "*<br>";
			if($matching_prompt == trim(preg_replace("/\s+/", " ", $prompt_listen_for[$key])))
			{
				//usleep(0250000);
				//echo "The matching prompt was found<br>";
				//echo "the key is " . $key . "<br>";
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
		//echo "The prompt score is less than the threshhold";
		
		//$theshhold_status = "The prompt score is less than the threshhold";
		//$current_scenario = $array_default_redirect_to_scenario[$current_scenario];
		
		//$current_scenario = $scenario_default_redirect_to_scenario;
		$departing_remarks = $scenario_default_remark;
		
		$srcs = $array_default_redirect_to_scenario[$current_scenario];
		$srdr = urldecode($array_default_remark[$current_scenario]);
	}
	//finished using the Sentence Recognition API

	//echo "The departing remarks are " . $srdr . "<br>";
	//echo "The new scenario is " . $srcs . "<br>";
	//echo "The response text is " . $array_say[$srcs] . "<br>";
	
	$xml_output .= "<departing_remarks>" . $srdr . "</departing_remarks>\n"; 
	
	//create random number up to count of array_say_array
	$max_say = count($array_say_array[$current_scenario]) - 1;
	$random_say = rand(0,$max_say);
	
	$xml_output .= "<say>" . $array_say_array[$current_scenario][$random_say] . "</say>\n"; 
	$xml_output .= "<new_scenario>" . $srcs . "</new_scenario>\n"; 
	}
	
	$end_time = time();
	
	//echo "<br>";
	$xml_output .= "<start_time>" . $start_time . "</start_time>\n";
	//echo "Started: " , $start_time , "<br>";
	//echo "Ended: " , $end_time , "<br>";
	$xml_output .= "<end_time>" . $end_time . "</end_time>\n";
	//echo "Time elapsed: " , ($end_time-$start_time) , " seconds<br>";
	$xml_output .= "<time_elapsed>" . ($end_time-$start_time) . "</time_elapsed>\n";
	
	$xml_output .= "</results>\n"; 
	
	//return (($WinningPrompt+1));
	
	echo $xml_output;
	
	return (0);
}

//beginning of the main body

//$start_time = time();

$error_msg = "";
$is_error = 0;

//get input from rest
if(isset( $_GET["input"]))
{
 $input = check_input($_GET["input"]); 
}
else
{
 $input = "";
 $is_error = 1;
 $error_msg = "No input was received.";
}

//get bot #
if(isset( $_GET["bot_id"]))
{
 $bot_id = check_input($_GET["bot_id"]); 
}
else
{
 $bot_id = "";
 $is_error = 1;
 $error_msg = "No bot ID was received.";
}

//get scenario #
if(isset( $_GET["scenario_id"]))
{
 $scenario_id = check_input($_GET["scenario_id"]); 
}
else
{
 $scenario_id = "";
 $is_error = 1;
 $error_msg = "No scenario ID was received.";
}

//get and verify key
if(isset( $_GET["key"]))
{
	$user_key = check_input($_GET['key']);
	if(validate_key($user_key) == true)
	{
		mysql_connect("localhost", "root", "Jurassic") or die(mysql_error());
		mysql_select_db("sentreco_api") or die(mysql_error());
		
		mysql_query("UPDATE api_keys SET Times_Called_Today = Times_Called_Today + 1 WHERE api_key = '$user_key'");
		mysql_query("UPDATE api_keys SET Times_Called_Ever = Times_Called_Ever + 1 WHERE api_key = '$user_key'");
		
		ProcessInput($input, $bot_id, $scenario_id);
	}
	else
	{
		$is_error = 1;
		$error_msg = "Invalid API Key";
	}
}
else
{
	$is_error = 1;
	$error_msg = "Invalid API Key";
}

if($is_error == 1)
{
	echo $error_msg;
}

mysqli_close($con);
mysql_close

//$end_time = time();

//echo "Started: " . $start_time . "<br>";
//echo "Ended: " . $end_time . "<br>";
//echo "Time elapsed: " . ($end_time-$start_time) . " seconds<br>";


?>