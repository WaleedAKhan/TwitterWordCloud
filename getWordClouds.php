<?php 

if($_POST['getCloud'] == "true"){
	
	
		//echo $_POST['testdata'];
		$dbconn = pg_connect("host=cs.utm.utoronto.ca dbname=khanwal1 user=khanwal1 password=1901501")
		or die('Could not connect: ' . pg_last_error());
		
		//retreive latest 5  toped liked wordclouds and their likes and authors
		$query = "SELECT * FROM searchResults order by likes desc limit 5";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());

		 //place the tuples from the query in an array -  there should be at most ten tuples
		$queryArray = array();
		while ($line = pg_fetch_array($result)) {
			$queryArray[] = $line;
		}
		
		//Get 5 newest shared posts
		$query = "SELECT * FROM searchResults order by searchid desc limit 5";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		
		while ($line = pg_fetch_array($result)) {
			$queryArray[] = $line;
		}
		
		// return a javascript array
		echo json_encode($queryArray);
	}

?>
