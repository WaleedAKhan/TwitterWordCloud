<?php 
session_save_path("sessions");
session_start();
include('searchfunctions.php');

$_SESSION['searchID'] = (string)$_POST["searchID"];
echo $_SESSION['searchID'];
return likeCloud();
	
?>
