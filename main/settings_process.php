<?php
	ob_start();
	//--------------------------------------------------------
	//start the session
	session_start();
	if (!isset($_SESSION['SESSION'])) require ("../includes/session_init.php");
	
	//include the required files and functions
	require_once("../includes/db_connection.php");
	require_once("../includes/functions.php");

	//set the time zone
	@date_default_timezone_set('Asia/Dacca');
	if(isset($_POST['submitLevel'])){
		$new_level = $_POST['accessCode'];
		$userName = $_POST['user'];
		$choice = $_POST['choice'];
		//now change the access level
		$query = "UPDATE users SET accessCode = '$new_level' WHERE userName = '$userName' ";
		$result = mysql_query($query);
		confirm_query($result);
		if($result)
		{
			header("Location:user_view.php?flag=epsilon&choice={$choice}");
			exit;
		}
		else
		{
			header("Location:user_view.php?flag=delta&choice={$choice}");
			exit;
		}
	}
	elseif(isset($_POST['submitPassword'])){
	$password = $_POST['password'];
	$userName = $_POST['user'];
	$choice = $_POST['choice'];
	$hashed_password = md5($password);
	if($password){
		if(strlen($password) >= 6){
			$query = "UPDATE users SET password = '$hashed_password' WHERE userName ='$userName' ";
			$result = mysql_query($query);
			confirm_query($result);
			if($result){
				header("Location:user_view.php?flag=epsilon&choice={$choice}");
				exit;
			}
			else{
				header("Location:user_view.php?flag=delta&choice={$choice}");
				exit;
			}
		}
		else{
			header("Location:user_view.php?flag=gama&choice={$choice}");
			exit;
		}
	}
	else{
		header("Location:user_view.php?flag=alpha&choice={$choice}");
		exit;
	}
	}
	//--------------------------------------------------------------------
	ob_flush(); 
?>