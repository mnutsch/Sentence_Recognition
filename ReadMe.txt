
OVERVIEW

The Sentreco2 API is a conversation tool which runs on top of the SentenceRecognition API. A program using the Sentreco2 API is expected to repeatedly pass user input to the API via REST. The API will return information in XML format. 

**********************

API ADDRESS

http://www.sentencerecognition.com/sentreco2.php

**********************

API CALL EXAMPLE

http://www.sentencerecognition.com/sentreco2.php?key=ae2b1fca515949e5d54fb22b8ed95575 6&bot_id=2&scenario_id=998&input=hello


http://www.sentencerecognition.com/sentreco2.php?key=232323&bot_id=2&scenario_id=998&input=hello

ae2b1fca515949e5d54fb22b8ed95575


**********************

REST VARIABLES

key = This is a key for the API to confirm that the application using it is authorized to do so.

bot_id = This is the id # for the XML data file. It will always stay the same for an API connection.

scenario_id = This is a number returned from the API. A program using the API should echo back the scenario_id given by the API in the last API call of the conversation. A program can send 998 for the first call in the conversation if the app should talk first. Sending scenario number 998 speaks the Say field for scenario 0. A return of scenario number 999 means that the application should end the user conversation.

input = This is a string of input text from the user. It may come from a typed prompt or from a speech to text interface.

**********************

XML File Format

<conversation> = The xml file should start and end with the tag "conversation"

<scenario> = Each step in the conversation is called a scenario. It contains the information for that step in the communication.

scenario - <id> = This should be a unique number to identify each scenario. Start with 0. The Say field in scenario 0 will be returned if the API is called with scenario 998. Scenario ID 999 is intended to notify the program that the 

scenario - <say> = This is the text that will be returned when the conversation moves to a new scenario. This text is intended to be displayed to the user or spoken through text to speech.

scenario - <threshold> = This is a numeric threshold between 0 and 100. If the input does not match a prompt to a score above this threshold then the default remark and default redirect to scenario will be returned.

scenario - <default_remark> = This is a departing remark which will be returned if the input's score does not exceed the threshold for any of the prompts. A departing remark is like Say, but it is expected that there may be a pause between the Departing Remark and the next Say. An example of a Departing Remark is "One moment please".

scenario - <default_redirect_to_scenario> = This is the scenario ID which will be returned if the input's score does not exceed the threshold for any of the prompts. The calling program is expected to echo the new scenario ID back to the API.

scenario - <prompt> = Each scenario has numerous statements which the user is expected to speak. The Prompt tag should wrap each of these expected statements.

scenario - prompt - <listen_for> = This is a string of text which it is expected that the user might speak/type as input.

scenario - prompt - <redirect_to_scenario> = This is the scenario ID which will be returned if this prompt best matches the user Input. The calling program is expected to echo the new scenario ID back to the API.

scenario - prompt - <departing_remarks> = This is a departing remark which will be returned if the user Input best matches this prompt. A departing remark is like Say, but it is expected that there may be a pause between the Departing Remark and the next Say. An example of a Departing Remark is "One moment please".

*******************

PSEUDO CODE FOR API USAGE - WITH SPEECH RECOGNITION AND TEXT TO SPEECH

Call API and send scenario_id # 998
Loop
{
	Read the API's XML response

	If the Scenario ID from the API's XML = 999
	{
		end the loop
	}

	If there are Departing Remarks in the XML response
	{
		Text To Speech the Departing Remarks text
		Pause briefly
	}

	Text To Speech the Say text

	Get Input from the user from Speech Recognition (possibly wait for command word)

	Call API, send Input from user and the last Scenario ID from the XML
}









