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
	$title =  "All Due Sales";

	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
		
	//determine the choice
	$new_choice = "List of all Due sales record";
		
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
							<span><?php echo strtoupper($new_choice); ?></span>
						</div>
						<div class="view">
							<?php
								if($_SESSION['STATUS'] == 1)
								{
									$type = "due_sales";
									if($_SESSION['LOGGEDIN'] == TRUE) { view_allSales($_SESSION['ACCESSCODE'],$type,$flag); }
									else { access_denied(); }
								}
								else {/*show that the user is inactive*/}
							?>
						</div>
					</div>
				</div>
				
<?php footer(); ?>

<?php ob_flush(); ?>
