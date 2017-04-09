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
	$title =  "Sales Search By Date";

	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
		
	//determine the choice
	$new_choice = "Type a date to search the sales inventory";
	
	//get the inventory records from the database
	if(isset($_POST['searchSales'])){
		$searchDay = $_POST['day'];
		$searchMonth = $_POST['month'];
		$searchYear = $_POST['year'];
		
		if($searchDay == "none" || $searchMonth == "none" || $searchYear == "none"){
			header("Location:sales_by_date.php?flag=nu");
			exit;
		}

		$billDate = $searchYear. "-" . $searchMonth . "-" . $searchDay ;
		$displayDate = $searchDay . "-" . $searchMonth . "-" . $searchYear;
		
		if($_SESSION['ACCESSCODE']=="green"){
			$query = "SELECT * FROM customers WHERE billDate = '{$billDate}' ORDER BY id";
		}
		else{
			header("Location:home.php"); 
			exit;
		}
		$result_set = mysql_query($query);
		confirm_query($result_set);
		$total_bills= mysql_num_rows($result_set);
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
									if($_SESSION['ACCESSCODE'] == "green") { search_salesInventory($flag,$_SESSION['ACCESSCODE']); }
									else { access_denied(); }
								}
								else {/*show that the user is inactive*/}
							?>
							<?php
							if(isset($_POST['searchSales'])){
								$row_color = 1;
								echo "
								<div class='extended_msg'>Sales Inventory details and settings for [ {$displayDate} ]</div>
								";
								echo "
								<span>
									<table align='center' width='100%' class='tbl' cellspacing='3px' cellpadding='3px'>
										<tr class='tblhead'>
											<td>Serial</td>
											<td>Bill ID</td>
											<td>Customer Name</td>
											<td>Cell No</td>
											<td>Bill Date</td>
											<td align='right'>Grand Total</td>
											<td align='right'>Total Due</td>
											<td>Due/Pay</td>
											<td>View</td>
											<td>S/H</td>
										</tr>
										<tr><td><br></td></tr>
								";
										while($rows=mysql_fetch_assoc($result_set)){
										if($rows['status'] == 1){$invert_status = "Hide";}
										elseif($rows['status']==0){$invert_status = "Show";}
										$grandTotal = number_format($rows['grandTotal'],2);
										$due = number_format($rows['grandTotal']-$rows['totalPaid'],2);
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
											<td>{$rows['customerName']}</td>
											<td>{$rows['customerCell']}</td>
											<td>{$rows['billDate']}</td>
											<td align='right'>{$grandTotal}</td>
											<td align='right'>{$due}</td>
											";
											if($_SESSION['ACCESSCODE'] == "green"){
												if($rows['dueStatus']==0){
													echo "
														<td><a href='sales_settings.php?bill=".$rows['bill_id']."&choice=due_bill' class='link_btn1'>Due</a></td>
													";
												}
												else{
													echo"
														<td><a href='sales_settings.php?bill=".$rows['bill_id']."&choice=pay_bill' class='link_btn2'>Pay</a></td>
													";
												}
												echo "
												<td><a href='sales_settings.php?bill=".$rows['bill_id']."&choice=view_bill' class='link_btn1'>View</a></td>
												<td align='right'><a href='sales_settings.php?bill=".$rows['bill_id']."&choice=sh_bill' class='link_btn2'>{$invert_status}</a></td> 
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
								<div class='extended_msg'>Total Bills: {$total_bills}</div>
								";
							}
							?>
						</div>
					</div>
				</div>
				
<?php footer(); ?>

<?php ob_flush(); ?>
