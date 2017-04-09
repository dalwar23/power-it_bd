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
	$title = "THANK YOU";
	
	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
		
	//make the log in time human read able
	$loginTime = date("d.m.Y H:i:s",$_SESSION['LOGINTIME']);
		
	//check for the GET values for the error message or success message
	$flag = "";
	$message= "";

	if(isset($_GET['flag'])) { $flag = $_GET['flag']; }
	
	//check the values to confirm the order
	if($_SESSION['customer_name'] == "type customer name" ||
		$_SESSION['customer_address'] == "type customer address" ||
		$_SESSION['customer_cell'] == "type customer cell no"){
		header("Location:cart.php");
		exit;
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
								<span>THANK YOU FOR THE ORDER ! <?php echo $_SESSION['customer_name'];?></span>
							</div>
							<div class="extended_msg">
									Total Items : <?php echo $_SESSION['total_items'] ;?> &nbsp; Total Price : <?php echo number_format($_SESSION['total_price'],2);?>
							</div>
							<div class="order_view">
								<?php
									if(is_array($_SESSION['cart'])){
										$query = "SELECT * FROM customers WHERE bill_id ='{$_SESSION['bill_id']}' ";
										$result = mysql_query($query);
										confirm_query($result);
										$rows = mysql_num_rows($result);
										if($rows == 0){
												$customerName = mysql_prep($_SESSION['customer_name']);
												$customerAddress = mysql_prep($_SESSION['customer_address']);
												$customerCell = mysql_prep($_SESSION['customer_cell']);
												$customerEmail_check = mysql_prep($_SESSION['customer_email']);
												if($customerEmail_check == "type customer e-mail"){$customerEmail == "N/A";}
												else{$customerEmail == $customerEmail_check;}
												$query = "INSERT INTO customers (id,bill_id,customerName,customerAddress,customerCell,customerEmail,billDate,billTime,grandTotal,totalPaid,preparedBy,status,dueStatus)
															   VALUES('','{$_SESSION['bill_id']}','{$customerName}','{$customerAddress}','{$customerCell}','{$customerEmail}','{$_SESSION['billDate']}','{$_SESSION['time']}','{$_SESSION['total_price']}','{$_SESSION['total_price']}','{$_SESSION['USERNAME']}','1','0')";
												$cust_result = mysql_query($query);
												confirm_query($cust_result);
												if($cust_result){
													increase_sale($_SESSION['USERNAME']);
													$max=count($_SESSION['cart']);
													for($i=0;$i<$max;$i++){
														$pid = mysql_prep($_SESSION['cart'][$i]['productid']);
														$qty = mysql_prep($_SESSION['cart'][$i]['qty']);
														$item_price = mysql_prep($_SESSION['cart'][$i]['unitPrice']);
														$product = get_product($pid);
														$productName = mysql_prep($product['productName']);
														$productType = mysql_prep($product['productType']);
														$productCode = mysql_prep($product['productCode']);
														$productWarranty = mysql_prep($product['warranty']);
														$productUnitPrice = $item_price ;
														$subTotal = $productUnitPrice * $qty;
														$bill_query = "INSERT INTO bill_details (id,bill_id,productName,productType,productCode,productWarranty,productQuantity,unitPrice,subTotal,billDate,status) VALUES('','{$_SESSION['bill_id']}','{$productName}','{$productType}','{$productCode}','{$productWarranty}','{$qty}','{$productUnitPrice}','{$subTotal}','{$_SESSION['billDate']}','1')";
														$bill_result = mysql_query($bill_query);
														confirm_query($bill_result);
														if($bill_result){
															deduct_qty($pid,$qty);
														}
														else{
															DIE("MySQL ERROR!");
														}
													}
													show_cart("confirm_cart");
												}
											else{
												DIE("MySQL ERROR!");
											}
										}
										else{
											show_cart("confirm_cart");
										}
									}
									else{
										echo "
										<div class='extended_msg'>
											Your order cart is empty! &nbsp;&nbsp;&nbsp;&nbsp; <a href='create_order.php?choice=select_cart' class='link_btn'>Continue Order</a>
										</div>
										";
									}
								?>
							</div>
					</div>
				</div>

<?php footer(); ?>

<?php ob_flush(); ?>