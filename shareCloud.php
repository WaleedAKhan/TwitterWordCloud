<?php
session_save_path("sessions");
session_start();
include('searchfunctions.php');

$_SESSION['searchWord'] = $_POST["searchTerm"];
$_SESSION['wordCloud'] = $_POST["wordCloud"];
echo $_SESSION['wordCloud'];
postCloud();
	

?>


