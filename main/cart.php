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
	$title = "CART";
	
	//find out the staus of the user
	if($_SESSION['STATUS'] == 1){ $userStatus = "ACTIVE"; }
	else{ $userStatus == "INACTIVE"; }
		
	//make the log in time human read able
	$loginTime = date("d.m.Y H:i:s",$_SESSION['LOGINTIME']);
	
	//check for the GET values for the error message or success message
	$flag = "";
	$message= "";

	if(isset($_GET['flag'])) { $flag = $_GET['flag']; }
	
	//update the cart values
	if(isset($_POST['submitCart'])){
		$max=count($_SESSION['cart']);
		for($i=0;$i<$max;$i++){
			$pid=$_SESSION['cart'][$i]['productid'];
			$db_unitprice = get_unitPrice($pid);
			$db_qty = get_dbqty($pid);
			$qty=intval($_POST['qty'.$pid]);
			$unitPrice = intval($_POST['unt'.$pid]);
			if($qty == 0){
				remove_product($pid);
			}
			else{
				if($db_qty >= $qty){
					$_SESSION['cart'][$i]['qty']=$qty;
				}
				else{
					$_SESSION['cart'][$i]['qty']=$db_qty;
				}
				if($unitPrice >= $db_unitprice){
					$_SESSION['cart'][$i]['unitPrice']=$unitPrice;
				}
				else{
					$_SESSION['cart'][$i]['unitPrice'] = $db_unitprice;
				}
			}
		}
		$_SESSION['total_items'] = total_items();
		$_SESSION['total_price'] = total_price();
		header("Location:cart.php");
	}
	//initialize the customer value
	if(empty($_SESSION['customer_cell']) || empty($_SESSION['customer_name'] ) || empty($_SESSION['customer_address'])){
		$_SESSION['customer_name'] = "type customer name";
		$_SESSION['customer_address'] = "type customer address";
		$_SESSION['customer_cell'] = "type customer cell no";
		$_SESSION['customer_email'] = "type customer e-mail";
	}
	
	//now get the customer values and assign them
	if(isset($_POST['submitCustomer'])){
		$customerName = $_POST['customerName'];
		$customerAddress = $_POST['customerAddress'];
		$customerCell = $_POST['customerCell'];
		$customerEmail = $_POST['customerEmail'];
		//assign them to the session variables
		$_SESSION['customer_name'] = $customerName;
		$_SESSION['customer_address'] = $customerAddress;
		$_SESSION['customer_cell'] = $customerCell;
		$_SESSION['customer_email'] = $customerEmail;
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
								<span>ORDER DETAILS - UPDATE ? / REMOVE ? ITEMS</span>
							</div>
							<div class="extended_msg">
									Total Items : <?php echo $_SESSION['total_items'] ;?> &nbsp; Total Price : <?php echo number_format($_SESSION['total_price'],2);?>
							</div>
							<div class="order_view">
								<?php
									if($_SESSION['cart']){
										show_cart("cart");
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