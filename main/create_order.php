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
	
	//set the title for the home page
	$title = "ORDER";
	
	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
	
	//check the GET values arrived or not, if not then send back to home page
	if(isset($_GET['choice'])) { $choice = $_GET['choice']; }
	elseif(isset($_POST['choice'])) { $choice = $_POST['choice']; }
	else { header("Location:home.php"); exit; }
	
	//determine the choice
	if($choice=="select_cart"){$new_choice = "Select a type to add item to the cart";}
	else{$new_choice = "Select a type to add item to the cart";}
	
	//set up the cart for order
	if($choice == "add_product"){
		$pid = $_GET['id'];
		add_to_cart($pid,1);
		$_SESSION['total_items'] = total_items();
		$_SESSION['total_price'] = total_price();
		header("Location:create_order.php?flag=xi&choice=select_cart");
	}
	
	//get the inventory records from the database
	if(isset($_POST['showRecord'])){
		$productType = $_POST['productType'];
		if($productType == "none"){
			header("Location:create_order.php?flag=kappa&choice={$choice}");
			exit;
		}
		
		$query = "SELECT * FROM products WHERE productType = '$productType' AND status = '1' ORDER BY id";
		$result_set = mysql_query($query);
		confirm_query($result_set);
		$total_product = mysql_num_rows($result_set);
	}
	
	//make the log in time human read able
	$loginTime = date("d.m.Y H:i:s",$_SESSION['LOGINTIME']);
	
	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus = "INACTIVE"; }
	
	//check for the GET values for the error message or success message
	$flag = "";
	$message= "";

	if(isset($_GET['flag'])) { $flag = $_GET['flag']; }
	
	//set up default cart values
	if(!isset($_SESSION['cart'])){
		$_SESSION['total_items'] = 0;
		$_SESSION['total_price'] = 0.00;
		$_SESSION['bill_id'] = get_bill_id();
		$_SESSION['date'] = date("d.m.Y");
		$_SESSION['billDate'] = date("Y-m-d");
	}
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
						<div class="label">
							<span>CREATE NEW ORDER</span>
						</div>
						<div class="extended_msg">
								Total Items : <?php echo $_SESSION['total_items'] ;?> &nbsp; Total Price : <?php echo number_format($_SESSION['total_price'],2);?>&nbsp;
								<a href="cart.php" class="link_btn">View Order</a>
						</div>
						<div class="view">
							<?php
								if($_SESSION['STATUS'] == 1)
								{
									if($_SESSION['LOGGEDIN'] == TRUE && $choice == "select_cart") { select_cart($choice,$flag,$_SESSION['ACCESSCODE']); }
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
										<table align='center' width='100%' class='tbl' cellspacing='3px' cellpadding='3px'>
											<tr class='tblhead'>
												<td>Serial</td>
												<td>Product ID</td>
												<td align='left'>Product Name</td>
												<td>Product Code</td>
												<td>Warranty</td>
												<td>Quantity</td>
												<td align='right'>Unit Price</td>
												<td>Add to cart ?</td>";
											echo"
												</tr>
												<tr><td><br></td></tr>
											";
											while($rows=mysql_fetch_assoc($result_set)){
											$unitPrice = number_format($rows['unitPrice'],2);
											if($row_color % 2==0){
												$tbl_class = "tbldata_even";
												$btn = "link_btn1";
											}
											else{
												$tbl_class="tbldata_odd";
												$btn = "link_btn2";
											}
											echo"
											<tr class='{$tbl_class}'>
												<td>{$row_color}</td>
												<td>{$rows['productId']}</td>
												<td align='left'>{$rows['productName']}</td>
												<td>{$rows['productCode']}</td>
												<td>{$rows['warranty']}</td>
												<td>{$rows['quantity']}</td>
												<td align='right'>{$unitPrice}</td>
												<td align='right'><a href='create_order.php?id=".$rows['id']."&choice=add_product' class='{$btn}'>Add to cart</a></td>";
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