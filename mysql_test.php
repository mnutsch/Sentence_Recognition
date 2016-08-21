<?php

	echo "mysql test<br>";
	mysql_connect("localhost", "root", "Jurassic") or die(mysql_error());
	mysql_select_db("sentreco_api") or die(mysql_error());
			
	$insert_result = mysql_query("INSERT INTO cached_responses (bot_id_sent, scenario_sent, input_sent, say_returned, new_scenario_returned, departing_remark_returned, process_time) VALUES ('the_bot_id', 'the_scenario_id', 'user_input', 'say_returned', 'new_scenario_returned', 'departing_remarks_returned', '1')") or die(mysql_error());

	$query_result = mysql_query("SELECT * FROM cached_responses");
	
	$cache_count = 0;
 	while($row = mysql_fetch_array($query_result))
	{
		//echo $row['FirstName'] . " " . $row['LastName']; //example
		//save to array
		//randomly select from array
		echo "Results Found"; //remove this
		$cache_count = $cache_count + 1;
	}

	mysql_close;
 
 ?>