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
	$title =  "User Settings";

	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
	
	//check the GET values arrived or not, if not then send back to home page
	if(isset($_GET['choice'])) { $choice = $_GET['choice']; }
	else { header("Location:home.php"); exit; }
	
	if(isset($_GET['user'])) { $user = $_GET['user']; }
	elseif(isset($_GET['productId'])) {$productId = $_GET['productId'];}
	//else { header("Location:home.php"); exit; }
	
	//determine the choice
	if($choice=="ad"){$new_choice = "Activation/Deactivation";}
	elseif($choice=="al"){$new_choice = "Change Access Level";}
	elseif($choice=="cp"){$new_choice = "Change Password";}
	elseif($choice=="edit"){$new_choice = "Edit details";}
	elseif($choice=="delete"){$new_choice = "Delete Details";}
	elseif($choice =="details"){$new_choice = "Details";}
	elseif($choice =="logs"){$new_choice = "Logs";}
	elseif($choice =="sh"){$new_choice = "Show/Hide";}
	elseif($choice =="edit_product"){$new_choice = "Edit and Update Product details";}
	elseif($choice =="delete_product"){$new_choice = "Delete Product details";}
	elseif($choice =="type_settings"){$new_choice = "Delete Product Types";}
	elseif($choice =="type_settings_view"){$new_choice = "View Product Types";}
	
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
									echo strtoupper($new_choice);
									if($choice != "edit_product" && $choice !="delete_product" && $choice != "sh" && $choice != "type_settings" && $choice != "type_settings_view"){
										echo " OF " . $user;
									}
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
									if($_SESSION['ACCESSCODE'] == "green" && $choice == "ad") { ad_user($choice,$flag,$user); }
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "al") { al_user($choice,$flag,$user); }
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "cp") { cp_user($choice,$flag,$user); }
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "edit") { edit_user($choice,$flag,$user); }
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "delete") { delete_user($choice,$flag,$user); }
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "logs") { user_logs($choice,$flag,$user); }
									elseif($_SESSION['LOGGEDIN'] == TRUE && $choice == "details") { view_details($choice,$flag,$user); }
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "sh") {sh_product($choice,$flag,$productId);}
									elseif(($_SESSION['ACCESSCODE'] == "green" || $_SESSION['ACCESSCODE'] == "yellow") && $choice == "edit_product") {edit_product($choice,$flag,$productId);}
									elseif(($_SESSION['ACCESSCODE'] == "green" || $_SESSION['ACCESSCODE'] == "yellow") && $choice == "type_settings") {delete_type($choice,$flag,$productId);}
									elseif($_SESSION['ACCESSCODE'] == "green" && $choice == "delete_product") {delete_product($choice,$flag,$productId);}
									elseif(($_SESSION['ACCESSCODE'] == "green" || $_SESSION['ACCESSCODE'] == "yellow" ) && $choice == "type_settings_view") { view_types($choice,$flag); }
									else { access_denied(); }
								}
								else {/*show that the user is inactive*/}
							?>
						</div>
					</div>
				</div>
				
<?php footer(); ?>

<?php ob_flush(); ?>
