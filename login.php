<?php
session_save_path("sessions");
session_start();

if($_POST['login']){
// Connecting, selecting database
$dbconn = pg_connect("host=cs.utm.utoronto.ca dbname=khanwal1 user=khanwal1 password=1901501")
    or die('Could not connect: ' . pg_last_error());
	
	$userName = $_POST['user'];
	$pwd = $_POST['pwd'];
	$salt = "@@$%%GDSDccc99900$$";
	//Check against the hashed pwd
	$hashedpwd =  sha1($salt . $pwd);
	
	$query = "SELECT username, password FROM userAccounts WHERE username=$1 AND password =$2";
	$result = pg_prepare($dbconn, "userLogin", $query);
	$result = pg_execute($dbconn, "userLogin", array($userName, $hashedpwd));
		
	if($row = pg_fetch_row($result)){
	$_SESSION['user'] = $row[0];
	$_SESSION['LoggedIn']=true;
	
	//Get Display Picture
	$query = "SELECT profilePic FROM userAccounts WHERE username=$1";
	$result = pg_prepare($dbconn, "getProfilePic", $query);
	$result = pg_execute($dbconn, "getProfilePic", array($userName));
	
	$row = pg_fetch_row($result);
	$_SESSION['imageLink'] = "images/".$row[0];

	
	
	//phpinfo();
	header('Location:landing.php');
	EXIT;
	}
	else{
	$_SESSION['LoggedIn']=false;
	echo"No Account exists. Please Register!";
	}
}
	
?>
<html>

<head>
<meta charset="UTF-8">
<title>Twitter Word Cloud</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="style.css">

</head>

<body class="container">
<table>
<tr>
<td>
<form method="post">
User Name:<input type="text" name="user"/><br>
Password:<input type="password" name="pwd"/><br>
<input type="submit" value="login" name="login">
</form>
</td> 
</tr>
</table>
New User?<a href="register.php"> Register!</a> 
</body>
</html>