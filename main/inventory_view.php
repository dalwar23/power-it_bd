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
	$title =  "View Invemtory";

	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
	
	//check the GET values arrived or not, if not then send back to home page
	if(isset($_GET['choice'])) { $choice = $_GET['choice']; }
	elseif(isset($_POST['choice'])) { $choice = $_POST['choice']; }
	else { header("Location:home.php"); exit; }
	
	//determine the choice
	if($choice=="select"){$new_choice = "Select a type to view/edit the Inventory";}
	else{$new_choice = "Select a type to view/edit the Inventory";}
	
	//get the inventory records from the database
	if(isset($_POST['showRecord'])){
		$productType = $_POST['productType'];
		if($productType == "none"){
			header("Location:inventory_view.php?flag=kappa&choice={$choice}");
			exit;
		}
		if($_SESSION['ACCESSCODE']=="green"){
			$query = "SELECT * FROM products WHERE productType = '{$productType}' ORDER BY id";
		}
		elseif($_SESSION['ACCESSCODE']=="yellow"){
			$query = "SELECT * FROM products WHERE productType = '$productType' AND status = '1' ORDER BY id";
		}
		$result_set = mysql_query($query);
		confirm_query($result_set);
		$total_product = mysql_num_rows($result_set);
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
							<div class='back'>
								<a href="home.php">Go back</a>
							</div>
						</div>
						<div class="view">
							<?php
								if($_SESSION['STATUS'] == 1)
								{
									if(($_SESSION['ACCESSCODE'] == "green" || $_SESSION['ACCESSCODE'] == "yellow") && ($choice == "select" || $choice=="sh" || $choice =="edit_product" || $choice == "delete_product")) { select_inventory($choice,$flag,$_SESSION['ACCESSCODE']); }
									else { access_denied(); }
								}
								else {/*show that the user is inactive*/}
							?>
							<?php
							if(isset($_POST['showRecord'])){
								$row_color = 1;
								echo "
								<div class='extended_msg'>Inventory details and settings for [ {$productType} ]</div>
								";
								echo "
								<span>
									<table align='center' width='100%' class='tbl' cell-spacing:3px cell-padding:3px>
										<tr class='tblhead'>
											<td>Product ID</td>
											<td>Product Name</td>
											<td>Product Code</td>
											<td>Warranty</td>
											<td>Quantity</td>
											<td align='right'>Unit Price</td>";
											if($_SESSION['ACCESSCODE'] == "green"){
												echo "
												<td align='right'>Total</td>
												<td>Entry By</td>
												<td>Edit</td>
												<td>Delete</td>
												<td>S/H</td> 
												";
											}
											elseif($_SESSION['ACCESSCODE'] == "yellow"){
												echo "
												<td>Edit</td>
												";
											}
										echo"
											</tr>
											<tr><td><br></td></tr>
										";
										while($rows=mysql_fetch_assoc($result_set)){
										if($rows['status'] == 1){$invert_status = "Hide";}
										elseif($rows['status']==0){$invert_status = "Show";}
										$total = number_format(($rows['quantity'] * $rows['unitPrice']),2);
										if($row_color % 2==0){
											$tbl_class = "tbldata_even";
										}
										else{
											$tbl_class="tbldata_odd";
										}
										$unit_price = number_format($rows['unitPrice'],2);
										echo"
										<tr class='{$tbl_class}'>
											<td>{$rows['productId']}</td>
											<td>{$rows['productName']}</td>
											<td>{$rows['productCode']}</td>
											<td>{$rows['warranty']}</td>
											<td>{$rows['quantity']}</td>
											<td align='right'>{$unit_price}</td>";
											if($_SESSION['ACCESSCODE'] == "green"){
												echo "
												<td align='right'>{$total}</td>
												<td><a href='settings.php?user=".$rows['entryBy']."&choice=details' target='_blank' class='link_btn1'>{$rows['entryBy']}</a></td>
												<td><a href='settings.php?productId=".$rows['productId']."&choice=edit_product' class='link_btn2'>Edit</a></td>
												<td><a href='settings.php?productId=".$rows['productId']."&choice=delete_product' class='link_btn1'>Delete</a></td>
												<td align='right'><a href='settings.php?productId=".$rows['productId']."&choice=sh' class='link_btn2'>{$invert_status}</a></td> 
												";
											}
											elseif($_SESSION['ACCESSCODE'] == "yellow"){
												echo "
												<td align='right'><a href='settings.php?productId=".$rows['productId']."&choice=edit_product' class='link_btn2'>Edit</a></td> 
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
								<div class='extended_msg'>Total Products: {$total_product}</div>
								";
							}
							?>
						</div>
					</div>
				</div>
				
<?php footer(); ?>

<?php ob_flush(); ?>
