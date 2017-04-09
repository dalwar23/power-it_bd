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
	
	//now get the POSTed values for usr creation
	if(isset($_POST['updateUser'])){
		$fullName = mysql_prep($_POST['fullName']);
		$address = mysql_prep($_POST['address']);
		$eMail = mysql_prep($_POST['eMail']);
		$displayName = mysql_prep($_POST['displayName']);
		$cellNo = mysql_prep($_POST['cellNo']);
		$userName = mysql_prep($_POST['userName']);
		$accessCode = mysql_prep($_POST['accessCode']);
		
		//get the hidden values from the page
		$choice = $_POST['choice'];

		//now insert the data into the database
		if($fullName && $eMail && $displayName && $cellNo && $userName && $accessCode)
		{
			$query = "UPDATE users SET 
						   fullname='{$fullName}',
						   address='{$address}',
						   eMail='{$eMail}',
						   displayName='{$displayName}',
						   cellNo='{$cellNo}'
						   WHERE userName = '{$userName}' ";
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
		else
		{
			header("Location:user_view.php?flag=alpha&choice={$choice}");
			exit;
		}
	}
	
	elseif(isset($_POST['deleteUser'])){
		//get the hidden values from the page
		$choice = mysql_prep($_POST['choice']);
		$userName = mysql_prep($_POST['user']);
		
		//query to delete the user
		$query = "DELETE FROM users WHERE userName = '{$userName}' ";
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
	
	elseif(isset($_POST['updateRecord'])){
		$productName = mysql_prep($_POST['productName']);
		$productCode = mysql_prep($_POST['productCode']);
		$quantity = mysql_prep($_POST['quantity']);
		$warranty = mysql_prep($_POST['warranty']);
		$unitPrice = mysql_prep($_POST['unitPrice']);
		
		//get the hidden values from the page
		$choice = $_POST['choice'];
		$productId = $_POST['productId'];
		
		if($productName && $quantity && $warranty && $unitPrice){
			$query = "UPDATE products SET
						   productName = '{$productName}',
						   productCode = '{$productCode}',
						   quantity = '{$quantity}',
						   warranty ='{$warranty}',
						   unitPrice = '{$unitPrice}'
						   WHERE productId = '{$productId}'
						  ";
			$result = mysql_query($query);
			confirm_query($result);
			if($result)
			{
				header("Location:inventory_view.php?flag=epsilon&choice={$choice}");
				exit;
			}
			else
			{
				header("Location:inventory_view.php?flag=delta&choice={$choice}");
				exit;
			}			
		}
		else
		{
			header("Location:inventory_view.php?flag=alpha&choice={$choice}");
			exit;
		}
	}
	
	elseif(isset($_POST['deleteRecord'])){
		//get the hidden values from the page
		$choice = $_POST['choice'];
		$productId = $_POST['productId'];
		
		//query to delete the user
		$query = "DELETE FROM products WHERE productId = '{$productId}' ";
		$result = mysql_query($query);
		confirm_query($result);
		if($result)
		{
			header("Location:inventory_view.php?flag=epsilon&choice={$choice}");
			exit;
		}
		else
		{
			header("Location:inventory_view.php?flag=delta&choice={$choice}");
			exit;
		}
	}
	
	elseif(isset($_POST['deleteType'])){
		$choice = mysql_prep($_POST['choice']);
		$productId = mysql_prep($_POST['productId']);
		$productType = mysql_prep($_POST['productType']);
		$query = "SELECT * FROM products WHERE productType = '{$productType}' ";
		$result = mysql_query($query);
		confirm_query($result);
		$total_products = mysql_num_rows($result);
		if($total_products == 0){
			$query = "DELETE FROM producttypes WHERE id = '{$productId}' ";
			$result = mysql_query($query);
			confirm_query($result);
			if($result){
				header("Location:settings.php?flag=tau&choice={$choice}");
				exit;
			}
			else{
				header("Location:settings.php?flag=delta&choice={$choice}");
				exit;
			}
		}
		else{
			header("Location:settings.php?flag=sigma&choice={$choice}");
			exit;
		}
	}
	//--------------------------------------------------------------------
	ob_flush(); 
?>