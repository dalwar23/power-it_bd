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
	
	//get the $_GET values for the cart and unset the values
	if(isset($_GET['choice'])) { $choice = $_GET['choice']; }
	if($choice=="cart_finish"){
		unset($_SESSION['cart']);
		unset($_SESSION['total_items']);
		unset($_SESSION['total_price']);
		unset($_SESSION['bill_id']);
		unset($_SESSION['date']);
		unset($_SESSION['billDate']);
		unset($_SESSION['time']);
		unset($_SESSION['customer_name']);
		unset($_SESSION['customer_address']);
		unset($_SESSION['customer_cell']);
		unset($_SESSION['customer_email']);
	}
	
	//set the title for the home page
	$title = "HOME";
	
	//make the log in time human read able
	$loginTime = date("d.m.Y H:i:s",$_SESSION['LOGINTIME']);
	
	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus = "INACTIVE"; }
?>

<?php common_header($title,$loginTime,$userStatus); ?>

				<div id="container">
					<div id="navigation_container">
						<div id="navigation">
						<?php
							if($_SESSION['STATUS'] == 1){
								if($_SESSION['ACCESSCODE'] == "green") { nav_green(); }
								elseif($_SESSION['ACCESSCODE'] == "yellow") { nav_yellow(); }
								elseif($_SESSION['ACCESSCODE'] == "red") { nav_red(); }
							}
							else {/*show that the user is inactive*/}
						?>
						</div>
					</div>
					<div id="content">
						<!-- Here goes the Home page view after login -->
					</div>
				</div>

<?php footer(); ?>

<?php ob_flush(); ?>