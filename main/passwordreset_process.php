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
	$_SESSION['LOGGEDIN'] = false;

	//set the time zone
	@date_default_timezone_set('Asia/Dacca');

	//get the posted values
	if(isset($_POST['email'])){ $eMail = mysql_prep($_POST['email']); }
	else{ header("Location:index.php"); exit; }

	//write the query to retrive the password from database
	$query = "SELECT * FROM users WHERE eMail='{$eMail}' ";
	$result = mysql_query($query);
	confirm_query($result);

	//select the row for the entityies
	$row = mysql_fetch_assoc($result);

	//check the number of affected rows
	$num_rows = mysql_num_rows($result);

	//now check the userName for existence
	if($num_rows > 0)
	{
		$mail = send_password($eMail);
		if($mail){
			echo "yes";
		}
		else{
			echo "no";
		}
	}
	else
		echo "no"; //Invalid login details
//--------------------------------------------------------------------
	ob_flush(); 
?>