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
	$title =  "Payment Search By Bill ID or Payment ID";

	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
		
	//determine the choice
	$new_choice = "Type a Bill ID or Payment ID to search the sales inventory";
	
	//get the posted values
	if(isset($_POST['searchPayments_byid'])){
		$searchType = mysql_prep($_POST['searchType']);
		$searchFor = mysql_prep($_POST['searchInput']);
		
		//check weather the search type is being selected or not
		if($searchType == "none"){
			header("Location:search_payments.php?flag=omikron");
			exit;
		}
		
		//check whether the search for field is being filled up or not
		if($searchFor == NULL){
			header("Location:search_payments.php?flag=upsilon");
			exit;
		}
		
		if($_SESSION['ACCESSCODE'] == "green"){
			$query = "SELECT * FROM payments WHERE $searchType = '{$searchFor}' ";
		}
		elseif($_SESSION['ACCESSCODE'] == "yellow" || $_SESSION['ACCESSCODE'] == "red"){
			$query = "SELECT * FROM payments WHERE $searchType = '{$searchFor}' AND status='1' ";
		}
		$result_set = mysql_query($query);
		confirm_query($result_set);
		$total_payments= mysql_num_rows($result_set);
	}
	
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
									if($_SESSION['LOGGEDIN'] == TRUE) { searchPayments_by_id($flag); }
									else { access_denied(); }
								}
								else {/*show that the user is inactive*/}
							?>
							<?php
							if(isset($_POST['searchPayments_byid'])){
								$row_color = 1;
								echo "
								<div class='extended_msg'>Payment details and settings for $searchType [ {$searchFor} ]</div>
								";
								echo "
								<span>
									<table align='center' width='100%' class='tbl' cellspacing='3px' cellpadding='3px'>
										<tr class='tblhead'>
											<td>Serial</td>
											<td>Bill ID</td>
											<td>Payment ID</td>
											<td>Customer Name</td>
											<td>Cell No</td>
											<td>Payment Date</td>
											<td>Paid Amount</td>
											<td>View</td>";
										if($_SESSION['ACCESSCODE'] == "green"){
											echo"
											<td>S/H</td>
											";
										}
										echo"
										</tr>
										<tr><td><br></td></tr>
										";
										while($rows=mysql_fetch_assoc($result_set)){
										if($rows['status'] == 1){$invert_status = "Hide";}
										elseif($rows['status']==0){$invert_status = "Show";}
										$customers = get_customer_details($rows['bill_id']);
										$paidAmount = number_format($rows['paidAmount'],2);
										if($row_color % 2==0){
											$tbl_class = "tbldata_even";
										}
										else{
											$tbl_class="tbldata_odd";
										}
										echo"
										<tr class='{$tbl_class}'>
											<td>{$row_color}</td>
											<td>{$rows['bill_id']}</td>
											<td>{$rows['payment_id']}</td>
											<td>{$customers['customerName']}</td>
											<td>{$customers['customerCell']}</td>
											<td>{$rows['paymentDate']}</td>
											<td>{$paidAmount}</td>
											";
											echo"
												<td align='right'><a href='sales_settings.php?bill=".$rows['bill_id']."&payment=".$rows['payment_id']."&choice=payment_receipt' class='link_btn1'>View</a></td>
											";
											if($_SESSION['ACCESSCODE'] == "green"){
												echo "											
												<td align='right'><a href='sales_settings.php?bill=".$rows['payment_id']."&choice=sh_payment' class='link_btn2'>{$invert_status}</a></td> 
												";
											}
										echo"
											</tr>
										";
										$row_color++;
										}
								echo "
									</table>
									</span>
								<div class='extended_msg'>Total Payments: {$total_payments}</div>
								";
							}
							?>
						</div>
					</div>
				</div>
				
<?php footer(); ?>

<?php ob_flush(); ?>
