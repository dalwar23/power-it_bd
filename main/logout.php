<?php
	//Start the ouput buffer
	ob_start();

	//start the session
	session_start();
	if (!isset($_SESSION['SESSION'])) require ("../includes/session_init.php");

	//include the required files and functions
	require_once("../includes/functions.php");


	//set the time zone
	@date_default_timezone_set('Asia/Dacca');
		
	//prevent direct access to logout.php file
	if($_SESSION['LOGGEDIN'] == false)
	{
		header("Location:index.php");
		exit;
	}
	//logsout from the page 
	if($_SESSION['LOGGEDIN'] == true)
	{
		//update the last login time with currently login time
		lastLoginTime_update($_SESSION['USERNAME'],$_SESSION['LOGINTIME']);
		//insert the logout of the account
		$time = time();
		access_logs($_SESSION['IPADDRESS'],$_SESSION['USERNAME'],"logout",$time);
		// Unset session data
		$_SESSION=array();
		// or...
		session_unset();
		//Destroy the session
		session_destroy();
		//redirect to index page
		header("Location:index.php");
		exit;
	}
	
	//flush the output buffer
	ob_flush();
?>