<?php ob_start(); ?>
<?php
	//start the session
	session_start();
	if (!isset($_SESSION['SESSION'])) require ("../includes/session_init.php");
	
	//required files and functions
	require_once("../includes/db_connection.php");
	require_once("../includes/functions.php");
	
	//check the browser doesn't cache the page
	header ("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	
	//set the time zone
	@date_default_timezone_set('Asia/Dacca');
	
	//check whether loggedin or not, if not then send back to login page
	if($_SESSION['LOGGEDIN'] == false)
	{
		header("Location:index.php");
		exit;
	}
	
	//make the log in time human read able
	$loginTime = date("d.m.Y H:i:s",$_SESSION['LOGINTIME']);
	
	//Set the title for the page
	$title =  "Sales Settings";

	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
	
	//check the GET values arrived or not, if not then send back to home page
	if(isset($_GET['choice'])) { $choice = $_GET['choice']; }
	else { header("Location:home.php"); exit; }
	
	if(isset($_GET['bill'])) {$bill_id = $_GET['bill'];}
	else { header("Location:home.php"); exit; }
	
	if(isset($_GET['payment'])) {$payment_id = $_GET['payment'];}
	
	//determine the choice
	if($choice=="view_bill"){$new_choice = "View bill";}
	elseif($choice =="sh_bill"){$new_choice = "Show/Hide bill";}
	elseif($choice =="due_bill"){$new_choice = "Make Due bill";}
	elseif($choice =="pay_bill"){$new_choice = "Make Payment";}
	elseif($choice =="payment_receipt"){$new_choice = "Payment Receipt";}
	
	//check for the GET values for the error message or success message
	$flag = "";
	$message= "";

	if(isset($_GET['flag'])) { $flag = $_GET['flag']; }
?>

<?php common_header($title,$loginTime,$userStatus); ?>

				<div id="container">
					<div id="navigation_container">
						<div id="navigation">
						<?php
							if($_SESSION['STATUS'] == 1)
								{
									if($_SESSION['ACCESSCODE'] == "green") { nav_green(); }
									elseif($_SESSION['ACCESSCODE'] == "yellow") { nav_yellow(); }
									elseif($_SESSION['ACCESSCODE'] == "red") { nav_red(); }
								}
								else {/*show that the user is inactive*/}
							
						?>
						</div>
					</div>
					<div id="content">
						<div class="label">
							<span>
								<?php 
									echo strtoupper($new_choice) . " OF " . $bill_id;
								?>
							</span>
							<div class='back'>
								<a href="home.php">Go back</a>
							</div>
						</div>
						<div class="view">
							<?php
								if($_SESSION['STATUS'] == 1)
								{
									if($_SESSION['LOGGEDIN'] == TRUE && $choice == "view_bill") { view_bill($bill_id); }
									elseif($_SESSION['LOGGEDIN'] == TRUE && $choice == "due_bill") { makeDue_bill($bill_id,$flag); }
									elseif($_SESSION['LOGGEDIN'] == TRUE && $choice == "pay_bill"){pay_bill($flag,$bill_id);}
									elseif($_SESSION['LOGGEDIN'] == TRUE && $choice == "payment_receipt"){payment_receipt($bill_id,$payment_id);}
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "sh_bill"){sh_bill($bill_id);}
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "sh_payment"){sh_payment($bill_id);}
									else { access_denied(); }
								}
								else {/*show that the user is inactive*/}
							?>
						</div>
					</div>
				</div>
				
<?php footer(); ?>

<?php ob_flush(); ?>