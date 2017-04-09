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
	if(isset($_POST['submitUser'])){
		$fullName = mysql_prep($_POST['fullName']);
		$eMail = mysql_prep($_POST['eMail']);
		$displayName = mysql_prep($_POST['displayName']);
		$cellNo = mysql_prep($_POST['cellNo']);
		$userName = mysql_prep($_POST['userName']);
		$password =mysql_prep($_POST['password']);
		$accessCode = mysql_prep($_POST['accessCode']);
		
		//get the hidden values from the page
		$choice = $_POST['choice'];

		//set the other necessary values
		$address = "";
		$status = 1;
		$creationDate = time();
		$lastLogin = time();
		$totalSale = 0;
		
		//now hash the password
		$hashedPassword = md5($password);

		//now insert the data into the database
		if($fullName && $eMail && $displayName && $cellNo && $userName && $password && $accessCode)
		{
			$query = "SELECT * FROM users WHERE userName = '{$userName}' ";
			$result = mysql_query($query);
			confirm_query($result);
			
			$rows = mysql_num_rows($result);
			if($rows != 0)
			{
				header("Location:create.php?flag=beta&choice={$choice}");
				exit;
			}
			else
			{
				if(strlen($password) >= 6)
				{
						$query = "INSERT INTO users 
						(id,fullName,address,cellNo,eMail,userName,password,displayName,accessCode,creationDate,lastLogin,totalSale,status)
					   VALUES(' ','$fullName','$address','$cellNo','$eMail','$userName','$hashedPassword','$displayName','$accessCode','$creationDate','$lastLogin','$totalSale','$status')";
						$result = mysql_query($query);
						confirm_query($result);
						if($result)
						{
							header("Location:create.php?flag=epsilon&choice={$choice}");
							exit;
						}
						else
						{
							header("Location:create.php?flag=delta&choice={$choice}");
							exit;
						}
				}
				else
				{
					header("Location:create.php?flag=gama&choice={$choice}");
					exit;
				}
			}
		}
		else
		{
			header("Location:create.php?flag=alpha&choice={$choice}");
			exit;
		}
	}
	elseif(isset($_POST['submitType'])){
		//now get the posted values for the type creation
		$typeName = mysql_prep($_POST['typeName']);
		$typeName = strtolower($typeName);
		
		//get the hidden values from the page
		$choice = $_POST['choice'];
		
		//now insert to the databse
		if($typeName)
		{
			//type hasbeen posted, insert into databasse but check for duplicate
			$query = "SELECT * FROM producttypes WHERE productType = '{$typeName}' ";
			$type_set = mysql_query($query);
			confirm_query($type_set);
			$no_of_same_type = mysql_num_rows($type_set);
			if($no_of_same_type != 0)
			{
				header("Location:create.php?flag=eta&choice={$choice}");
				exit;
			}
			else
			{
				$query = "INSERT INTO producttypes (id,productType,status) VALUES('','$typeName','1')";
				$result = mysql_query($query);
				confirm_query($result);
				if($result)
				{
					header("Location:create.php?flag=epsilon&choice={$choice}");
					exit;
				}
				else
				{
					header("Location:create.php?flag=delta&choice={$choice}");
					exit;
				}
			}
		}
		else
		{
			header("Location:create.php?flag=alpha&choice={$choice}");
			exit;
		}
	} 
	elseif(isset($_POST['submitRecord'])){
		//now get the POSTed values
		$productName = mysql_prep($_POST['productName']);
		$productType = mysql_prep($_POST['productType']);
		$productCode = mysql_prep($_POST['productCode']);
		$quantity = mysql_prep($_POST['quantity']);
		$warranty = mysql_prep($_POST['warranty']);
		$unitPrice = mysql_prep($_POST['unitPrice']);
		
		//get the hidden values from the page
		$choice = $_POST['choice'];
		
		//now get the other values for ther record
		$productId = get_product_id($productType);
		$entryBy = $_SESSION['USERNAME'];
		$entryDate = time();
		$searchDate = date("Y-m-d");
		$status = 1;
		
		
		if($productName && $productType && $quantity && $warranty && $unitPrice)
		{
			if($productType == "none"){
				header("Location:create.php?flag=lambda&choice={$choice}");
				exit;
			}
			else{
				$query = "INSERT INTO products (id,productId,productName,productType,productCode,quantity,warranty,unitPrice,entryBy,entryDate,searchDate,status) 
							  VALUES('','$productId','$productName','$productType','$productCode','$quantity','$warranty','$unitPrice','$entryBy','$entryDate','$searchDate','$status')";
				$result = mysql_query($query);
				confirm_query($result);
				if($result)
				{
					header("Location:create.php?flag=epsilon&choice={$choice}");
					exit;
				}
				else
				{
					header("Location:create.php?flag=delta&choice={$choice}");
					exit;
				}
			}
		}
		else
		{
			header("Location:create.php?flag=alpha&choice={$choice}");
			exit;	
		}
	}
	
	elseif(isset($_POST['submitDue'])){
		//Get the POSTed values
		$bill_id = mysql_prep($_POST['bill_id']);
		$payment_pre = mysql_prep($_POST['payment']);
		$grandTotal = mysql_prep($_POST['grandTotal']);
		
		if($payment_pre >= $grandTotal){
			$payment = $grandTotal;
			$db_duestat = 0;
		}
		else{
			$payment = $payment_pre;
			$db_duestat =1 ;
		}
		//now check whether payment is made or not
		if($bill_id && $payment){
			$query = "UPDATE customers SET totalPaid='{$payment}' , dueStatus = '{$db_duestat}' WHERE bill_id = '{$bill_id}' ";
			$result = mysql_query($query);
			confirm_query($result);
			if($result){
				header("Location:sales_settings.php?bill={$bill_id}&choice=view_bill");
				exit;
			}
			else{
				header("Location:sales_settings.php?bill={$bill_id}&flag=delta&choice=due_bill");
				exit;
			}
		}
		else{
			header("Location:sales_settings.php?bill={$bill_id}&flag=rho&choice=due_bill");
			exit;
		}
	}
	
	elseif(isset($_POST['submitPayment'])){
		//get payment id and date
		$payment_id = get_payment_id();
		$paymentDate = date("Y-m-d");
		//Get the POSTed Values
		$bill_id = mysql_prep($_POST['bill_id']);
		$totalPaid = mysql_prep($_POST['totalPaid']);
		$grandTotal = mysql_prep($_POST['grandTotal']);
		$db_due = $grandTotal - $totalPaid;
		$newPayment_pre = mysql_prep($_POST['newPayment']);
		if($db_due >= $newPayment_pre){
			$newPayment = $newPayment_pre;
		}
		else{
			$newPayment = $db_due;
		}
		//add the previous payment and new payment to get the new total paid
		$newTotalPaid = $totalPaid + $newPayment ;	
		//find out the due payment
		$totalDue = $grandTotal - $newTotalPaid;
		//execute the query to make the payment
		if($bill_id && $totalPaid && $newPayment){
			if($newTotalPaid == $grandTotal){
				$query = "UPDATE customers SET totalPaid='{$newTotalPaid}', dueStatus='0' WHERE bill_id='{$bill_id}' ";
			}
			else{
				$query = "UPDATE customers SET totalPaid='{$newTotalPaid}' WHERE bill_id='{$bill_id}' ";
			}
			$result = mysql_query($query);
			confirm_query($result);
			if($result){
				$query = "INSERT INTO payments (id,bill_id,payment_id,paidAmount,totalPaid,totalDue,paymentDate,status)
							   VALUES ('','{$bill_id}','{$payment_id}','{$newPayment}','{$newTotalPaid}','{$totalDue}','{$paymentDate}','1')
							 ";
				$result_set = mysql_query($query);
				confirm_query($result_set);
				if($result_set){
					header("Location:sales_settings.php?bill={$bill_id}&payment={$payment_id}&choice=payment_receipt");
					exit;
				}
			}
			else{
				header("Location:sales_settings.php?bill={$bill_id}&flag=delta&choice=due_bill");
				exit;
			}
		}
		else{
			header("Location:sales_settings.php?bill={$bill_id}&flag=rho&choice=pay_bill");
			exit;
		}
	}
	
	//--------------------------------------------------------------------
	ob_flush(); 
?>