<?php
	ob_start();
	//--------------------------------------------------------
	
	//include the required files and functions
	require_once("../includes/db_connection.php");
	require_once("../includes/functions.php");

	//start the session
	session_start();
	if (!isset($_SESSION['SESSION'])) require ("../includes/session_init.php");

	// reset session variables...
	$_SESSION['DISPLAYNAME'] = "";
	$_SESSION['USERNAME'] = "";
	$_SESSION['ACCESSCODE'] = "";
	$_SESSION['STATUS'] = "";
	$_SESSION['LOGGEDIN'] = false;
	$_SESSION['IPADDRESS'] = "";
	$_SESSION['LOGINTIME'] = "";
	$_SESSION['LASTLOGIN'] = "";

	//set the time zone
	@date_default_timezone_set('Asia/Dacca');

	//get the posted values
	if(isset($_POST['user_name'])){ $userName = mysql_prep($_POST['user_name']); }
	else{ header("Location:index.php"); exit; }
	if(isset($_POST['password'])) { $password = mysql_prep($_POST['password']); }
	else { header("Location:index.php"); exit; }

	//hash the password to compare with the database password
	$hashed_password = md5($password);

	//write the query to retrive the password from database
	$query = "SELECT * FROM users WHERE userName='".$userName."'";
	$result = mysql_query($query);
	confirm_query($result);

	//select the row for the entityies
	$row = mysql_fetch_assoc($result);

	//check the number of affected rows
	$num_rows = mysql_num_rows($result);

	//now check the userName for existence
	if($num_rows > 0)
	{
		//compare the password and username
		if(strcmp($row['password'],$hashed_password) == 0)
		{
			echo "yes";
			
			//set the session variables
			$_SESSION['DISPLAYNAME'] = $row['displayName'];
			$_SESSION['USERNAME'] = $userName;
			$_SESSION['ACCESSCODE'] = $row['accessCode'];
			$_SESSION['STATUS'] = $row['status'];
			$_SESSION['LOGGEDIN'] = true;
			$_SESSION['IPADDRESS'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['LOGINTIME'] = time();
			$_SESSION['LASTLOGIN'] = date("d.m.Y H:i:s",$row['lastLogin']);
			
			//enter the access logs 
			access_logs($_SESSION['IPADDRESS'],$_SESSION['USERNAME'],"login",$_SESSION['LOGINTIME']);
		}
		else
			echo "no"; //invalid login details
	}
	else
		echo "no"; //Invalid login details
//--------------------------------------------------------------------
	ob_flush(); 
?>