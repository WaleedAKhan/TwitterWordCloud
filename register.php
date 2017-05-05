<?php
error_reporting(0);
if($_POST['register']){
	
//Connect to the database
$dbconn = pg_connect("host=cs.utm.utoronto.ca dbname=khanwal1 user=khanwal1 password=1901501")
    or die('Could not connect: ' . pg_last_error());

$userName = $_POST['user'];
$pwd = $_POST['pwd'];
$fName = $_POST['fName'];
$lName = $_POST['lName'];

//Remove Whitespace if present
$userName = trim($userName);
$pwd = trim($pwd);
$fName = trim($fName);
$lName = trim($lName);


//A salt to use for SHA1 hashing the plain-text password
$salt = "@@$%%GDSDccc99900$$";
$hashedpwd =  sha1($salt . $pwd);

$query = "SELECT username FROM userAccounts WHERE username = $1";
$result = pg_prepare($dbconn, "checkIfUserExists", $query);
$result = pg_execute($dbconn, "checkIfUserExists", array($userName));

if($row = pg_fetch_row($result)){
echo "User Name already exists, please try another.";
}
else{

	if(!is_uploaded_file($_FILES["profilePic"]["tmp_name"])){
		$file_name = "default";
	}

	else{
	//Upload Image to Server, if any, otherwise use default
	$file_tmp = $_FILES["profilePic"]["tmp_name"];
	$file_name = $userName."profilePic";
	move_uploaded_file($file_tmp, "images/".$file_name);
	//Change permissions so that file can be seen on web site
	chmod ("images/".$file_name , 0744);
	}
	
//Insert User into DB
$query = "INSERT INTO userAccounts (username, password, fName, lName, profilePic) values ($1, $2, $3, $4, $5)";
$result = pg_prepare($dbconn, "createUser", $query);
$result = pg_execute($dbconn, "createUser", array($userName, $hashedpwd, $fName, $lName, $file_name));



header('Location:login.php');
}
		
}
?>

<html>
<head>
<meta charset="UTF-8">
<title>Twitter Word Cloud</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>


<form method="post" enctype="multipart/form-data">
<table style="width:100%">
<tr>
<td>User Name:<input type="text" name="user"/></td>
</tr>
<tr>
<td>Password:<input type="password" name="pwd"/></td>
</tr>
<tr>
<td>Profile Picture:<input type="file" name="profilePic"/></td>
</tr>
<tr>
<td><input type="submit" value="Sign Up" name="register"></td>
</tr>
</table>
</form>


</body>
</html>