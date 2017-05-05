<?php
session_save_path("sessions");
session_start();
//ini_set('display_errors', 1);
include('searchfunctions.php');

if($_POST['searchType'] == "keyword"){
	//echo "<html>".$_POST["keyWordSearch"]."</html>";
	$_SESSION['searchWord'] = $_POST["searchTerm"];
	//echo $_SESSION['searchWord'];
	echo showKeyWordCloud();
	
// handle keyword search and eacho html string keyowrd  
}


if($_POST['searchType'] == "userNameSearch"){
// handle username search and eacho html string keyowrd  
	$_SESSION['searchWord'] = $_POST["searchTerm"];
	echo showUserTweetsCloud();
}

?>


