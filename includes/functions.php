<?php
/*-----------------------------------------------------------------
//This block of PHP uses the required files to load
-----------------------------------------------------------------*/
require_once("db_connection.php");

/*-----------------------------------------------------------------
//This block of PHP will star the session in this page
------------------------------------------------------------------*/
session_start();
if (!isset($_SESSION['SESSION'])) require ("session_init.php");

/*-----------------------------------------------------------------
//This function confirms the query is executed perfectly
-----------------------------------------------------------------*/
function confirm_query($result_set){
	if(!$result_set){
		die("Database Query failed ! " ."<br> Error no: " . mysql_errno() . "<br>Error: ". mysql_error());
	}
}

/*-----------------------------------------------------------------
//This function generates error messages / messages
------------------------------------------------------------------*/
function messages($flag){
	switch($flag){
		case "alpha":
			$message = "Atleast one of the fields is blank";
		break;
		case "beta":
			$message = "username already exists! please try another one!";
		break;
		case "gama":
			$message = "Provided password is too short! Password must be at least 6 character long.";
		break;
		case "delta":
			$message = "There is a problem with mySQL query! Please try again later.";
		break;
		case "epsilon":
			$message = "Data has been processed successfully!";
		break;
		case "zeta":
			$message = "The form is not being submitted properly!";
		break;
		case "eta":
			$message = "Product type already exists! please try another one!";
		break;
		case "theta":
			$message = "You can't change the access level of an administrator. Please contact super admin!";
		break;
		case "iota":
			$message = "You can't delete an administrator. Please contact super admin!";
		break;
		case "kappa":
			$message = "You didn't select any type! Please select a type to view the inventory.";
		break;
		case "lambda":
			$message = "You didn't select any type! Please select a type to create a new record.";
		break;
		case "mu":
			$message = "The following record is either empty or contains more then one product, for deleting it must have only one product.";
		break;
		case "nu":
			$message = "Day - Month - Year format is not valid! Please select correct date.";
		break;
		case "xi":
			$message = "Product has been added to the cart.";
		break;
		case "omikron":
			$message = "You didn't select any type! Please select a type to view the sales records.";
		break;
		case "pi":
			$message = "You didn't write any cell no or bill ID! Please write cell no or bill id to view the sales records.";
		break;
		case "rho":
			$message = "You didn't write any payment or entered 0. Please try again!";
		break;
		case "sigma":
			$message = "This product type has 1 or More Products in the Database. You can't delete this product type.";
		break;
		case "tau":
			$message = "The product type has been successfully deleted from the database.";
		break;
		case "upsilon":
			$message = "You didn't write any Bill ID or Payment ID! Please write Bill ID or Payment ID to view the Payment records.";
		break;
		case "phi":
			$message = "You are trying to Show/Hide a Due Invoice. You can't Show/Hide a Due Invoice.";
		break;
		case "chi":
			$message = "You can't Activate/Deactivate an Administrator's account.";
		break;
		default:
			$message=" ";
	}
	return $message;
}

/*-----------------------------------------------------------------
// This function prevents mySQL injections
-----------------------------------------------------------------*/
function mysql_prep($value){
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists("mysql_real_escape_string"); //i.e. PHP >= v4.3.0
	if($new_enough_php){ //PHP v4.3.0 or higher
		//undo any magic quote effects so mysql_real_escape_string can do the work
		if($magic_quotes_active){
			$value = stripslashes($value);
		}else {
			$value = mysql_real_escape_string($value);
		}
	}else { //before PHP v4.3.0
		//if magic quotes aren't already on then addslashes manually
		if(!$magic_quotes_active){
			$value = addslashes($value);
		} //if magic quotes are active then the slashes already exists;
	}
	return $value;
}

/*----------------------------------------------------------------
//this function keeps track of login/logout 
----------------------------------------------------------------*/
function access_logs($ip,$userName,$type,$time){
	$query = "INSERT INTO accesslogs
				  (id,userName,ipAddress,type,time)
				  VALUES ('','$userName','$ip','$type','$time')";
	$result = mysql_query($query);
	confirm_query($result);
}

/*----------------------------------------------------------------
//This function will update the last login time of the user
----------------------------------------------------------------*/
function lastLoginTime_update($userName,$time){
	$query = "UPDATE users
				   SET lastLogin = '$time'
				   WHERE userName = '$userName'";
	$result = mysql_query($query);
	confirm_query($result);
}

/*----------------------------------------------------------------
//This function will generate the navigation pannel for the user
//having access code GREEN
----------------------------------------------------------------*/
function nav_green(){
echo "
	<ul class='menu'>
		<li class='open'><span>Navigation Menu<span></li>
		<li class='open'><a href='#'class='open_menu'>User Management</a>
			<ul class='submenu'>
				<li><a href='create.php?choice=user' >Create New User</a></li>
				<li><a href='user_view.php?choice=details'>View All Users</a></li>
				<li><a href='user_view.php?choice=edit_delete'>Update - Delete User</a></li>
				<li><a href='user_view.php?choice=settings'>Change User Settings</a></li>
				<li><a href='user_view.php?choice=logs'>View User Logs</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Inventory Management</a>
			<ul class='submenu'>
				<li><a href='create.php?choice=type'>Create New Product Type</a></li>
				<li><a href='create.php?choice=record'>Create New Inventory Record</a></li>
				<li><a href='settings.php?choice=type_settings_view'>Product Type Settings</a></li>
				<li><a href='inventory_view.php?choice=select'>Inventory Record Settings</a></li>
				<li><a href='search_view.php?choice=search'>Search Inventory By Date</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Order Management</a>
			<ul class='submenu'>
				<li><a href='create_order.php?choice=select_cart'>Create New Order</a></li>
				<li><a href='search_payments.php'>Payment Receipts</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Sales Report Management</a>
			<ul class='submenu'>
				<li><a href='all_sales.php'>View All Sales Reports</a></li>
				<li><a href='sales_by_date.php'>View Sales Report [By Date]</a></li>
				<li><a href='sales_by_id.php'>View Sales Report [By Type]</a></li>
				<li><a href='sales_by_due.php'>View Sales Report [Due]</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Hangout</a>
			<ul class='submenu'>
				<li><a href='#'>Chat</a></li>
				<li><a href='#'>Webmail</a></li>
			</ul>
		</li>
	</ul>
";
}

/*----------------------------------------------------------------
//This function will generate the navigation pannel for the user
//having access code YELLOW
----------------------------------------------------------------*/
function nav_yellow(){
echo "
	<ul class='menu'>
		<li class='open'><span>Navigation Menu<span></li>
		<li class='open'><a href='#'class='open_menu'>Order Management</a>
			<ul class='submenu'>
				<li><a href='create_order.php?choice=select_cart'>Create New Order</a></li>
				<li><a href='search_payments.php'>Payment Receipts</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Sales Report</a>
			<ul class='submenu'>
				<li><a href='sales_by_id.php'>View Sales Report [By Type]</a></li>
				<li><a href='sales_by_due.php'>View Sales Report [Due]</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Inventory Management</a>
			<ul class='submenu'>
				<li><a href='create.php?choice=type'>Create New Product Type</a></li>
				<li><a href='create.php?choice=record'>Create New Inventory Record</a></li>
				<li><a href='settings.php?choice=type_settings_view'>Product Type Settings</a></li>
				<li><a href='inventory_view.php?choice=select'>Inventory Record Settings</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Hangout</a>
			<ul class='submenu'>
				<li><a href='#'>Chat</a></li>
				<li><a href='#'>Webmail</a></li>
			</ul>
		</li>
	</ul>
";
}

/*----------------------------------------------------------------
//This function will generate the navigation pannel for the user
//having access code RED
----------------------------------------------------------------*/
function nav_red(){
echo "
	<ul class='menu'>
		<li class='open'><span>Navigation Menu<span></li>
		<li class='open'><a href='#' class='open_menu'>Order Management</a>
			<ul class='submenu'>
				<li><a href='create_order.php?choice=select_cart'>Create New Order</a></li>
				<li><a href='search_payments.php'>Payment Receipts</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Sales Report</a>
			<ul class='submenu'>
				<li><a href='sales_by_id.php'>View Sales Report [By Type]</a></li>
				<li><a href='sales_by_due.php'>View Sales Report [Due]</a></li>
			</ul>
		</li>
		<li class='open'><a href='#' class='open_menu'>Hangout</a>
			<ul class='submenu'>
				<li><a href='#'>Chat</a></li>
				<li><a href='#'>Webmail</a></li>
			</ul>
		</li>
	</ul>
";
}

/*---------------------------------------------------------------------
//This is the index header function of the site [before login]
---------------------------------------------------------------------*/
function index_header(){
echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<title>Login | Power IT Limited</title>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' >
		<meta name='description' content='Power IT Limited' >
		<meta name='keywords' content='web,web design,networking,bd networking, web solutions, business, business web,computer,motherboard,ram,speaker' >
		<meta name='author' content='arif, binarydreamers.net' >
		<script src='../js/jquery.js' type='text/javascript' language='javascript'></script>
		<script src='../js/cufon-yui.js' type='text/javascript'></script>
		<script src='../js/cufon-replace.js' type='text/javascript'></script>
		<script src='../js/Myriad_Pro_300.font.js' type='text/javascript'></script>
		<link rel='stylesheet' href='../css/style.css' type='text/css'>
";
}

/*---------------------------------------------------------------------
//This is the index body function of the site [before login]
---------------------------------------------------------------------*/
function index_body_top(){
echo "
	</head>
	<body>
		<div id='wrapper'>
			<div id='mainContainer'>
				<div id='header'>
					<div id='header_top'></div>
					<div id='header_bottom'>
						<a href=''>
							<img src='../images/Login_logo.jpg' alt='login_logo' title='Login'>
						</a>
							<div id='total_botom'>
								<div id='bottom_left'>
									<h3>Login</h3>
									<p>
										to&nbsp;<a href='http://www.poweritbd.com'>poweritbd.com</a> Administration area, it's a complete, fully functional customizeable administrative
										area for <a href='http://www.poweritbd.com'>poweritbd.com</a> employees.
									</p>
								</div>
							</div>
					</div>
				</div>
";
}

/*----------------------------------------------------------------
//This is the header function of the site [after login]
-----------------------------------------------------------------*/
function common_header($title,$loginTime,$userStatus){
$accessCode = strtoupper($_SESSION['ACCESSCODE']);
echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<title>{$title} | Power IT Limited</title>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' >
		<meta name='description' content='Power IT Limited' >
		<meta name='keywords' content='web,web design,networking,bd networking, web solutions, business, business web,computer,motherboard,ram,speaker' >
		<meta name='author' content='arif, binarydreamers.net' >
		<script src='../js/jquery.tools.min.js' type='text/javascript'></script>
		<script src='../js/togglemenu.js' type='text/javascript'></script>
		<script src='../js/cufon-yui.js' type='text/javascript'></script>
		<script src='../js/cufon-replace.js' type='text/javascript'></script>
		<script src='../js/Myriad_Pro_300.font.js' type='text/javascript'></script>
		<link rel='stylesheet' href='../css/style.css' type='text/css' media='screen'>
		<link rel='stylesheet' href='../css/print.css' type='text/css' media='print'> 		
	</head>
	<body>
		<div id='wrapper'>
			<div id='mainContainer'>
				<div id='header'>
					<div id='header_top'>
						<p>
							<a href='index.php'>Home</a>
							<a href='settings.php?user=".$_SESSION['USERNAME']."&choice=details'>Profile</a>
							<a href='logout.php' class='bg_none'>Logout</a>				
						</p>
					</div>
					<div id='header_bottom'>
						<a href=''>
							<img src='../images/Login_logo.jpg' alt='login_logo'>
						</a>
							<div id='total_botom'>
								<div id='bottom_left'>
									<h3>Welcome !  {$_SESSION['DISPLAYNAME']} </h3>
									<p>
										to&nbsp;<a href='http://www.poweritbd.com'>poweritbd.com</a> Administration area, it's a complete, fully functional, simple &amp; customizeable administrative
										area for <a href='http://www.poweritbd.com'>poweritbd.com</a> employees.
									</p>
								</div>
								<div id='bottom_right'>
									<h3>ACCESS CODE : {$accessCode} </h3>
									<p>User : <strong> {$_SESSION['USERNAME'] } [ {$userStatus } ] </strong></p>
									<p class='last'>Logged in &#64; :  {$loginTime}</p>
									<p class='last'>Last Login :  {$_SESSION['LASTLOGIN']}</p>
								</div>
							</div>
					</div>
				</div>
";
}

/*----------------------------------------------------------------
//This is the footer function for the site [general]
-----------------------------------------------------------------*/
function footer(){
$year = date("Y");
echo  "
				<div id='footer'>
					<p class='footer_left'>
						All Rights Reserved.<br>&copy; Copyright ( 2007 - $year ) - power&nbsp;IT.<br>Designed & Developed by: arif
					</p>
					<div id='footer_right'>
						<p>
							Powered by: <a href='http://www.binarydreamers.net'>binaryDreamers Inc.</a>
						</p>
						<img src='../images/footer_right_bar.jpg' height='37' width='26'/>
					</div>
				</div>
			</div>
		</div>
		<script type='text/javascript'> Cufon.now(); </script>
	</body>
</html>
";
}

/*------------------------------------------------------------------
//This function will create new user reg. form
-------------------------------------------------------------------*/
function create_user($choice,$flag){
$message = messages($flag);
$no_of_users = get_all_the_users();
echo "
<div class='msg'>{$message}</div>
<form id='myform' class='cols' method='POST' action='create_process.php' name='create_user'>
	<div class='alpha'>
			<label> full name * <input type='text' name='fullName' maxlength='30' required='required' autocomplete='on' autofocus='autofocus'/> </label>
			<label> email * <input type='email' required='required' minlength='9' name='eMail' autocomplete='on'/> </label> 
			<label> display name * <input type='text' name='displayName' maxlength='16' required='required' autocomplete='on'/> </label>
			<label> cell no * <input type='number' name='cellNo' maxlength='16' required='required' autocomplete='on'/> </label>
	</div>
	<div class='beta'>
			<label> username * <input type='text' name='userName' minlength='6' required='required' autocomplete='on'/> </label>
			<label> Password * <input type='password' name='password' minlength='6' required='required'/> </label> 
			<label> Password check <input type='password' name='check' data-equals='password' /> </label>
			<input type='hidden' name='choice' value='{$choice}' id='choice'/><br>
			<label> access code *
				<select name='accessCode'>
					  <option value='yellow'>yellow</option>
					  <option value='red'>red</option>
				</select>
			</label>
	</div>

	<div class='clear'></div>
	
	<button type='submit' id='create' name='submitUser'>Create new user</button>
</form>
<div class='msg'>Total ACTIVE users : {$no_of_users}</div>

<script> 
$.tools.validator.fn('[data-equals]', 'Value not equal with the $1 field', function(input) {
	var name = input.attr('data-equals'),
		 field = this.getInputs().filter('[name=' + name + ']'); 
	return input.val() == field.val() ? true : [name]; 
});

$.tools.validator.fn('[minlength]', function(input, value) {
	var min = input.attr('minlength');
	
	return value.length >= min ? true : {     
		en: 'Please provide at least ' +min+ ' character' + (min > 1 ? 's' : ''),
		fi: 'Kentän minimipituus on ' +min+ ' merkkiä' 
	};
});

$('#myform').validator({ 
	position: 'top left', 
	offset: [-12, 0],
	message: '<div><em/></div>' 
});
</script>

";
}

/*------------------------------------------------------------------
//This function will create new type form
-------------------------------------------------------------------*/
function create_type($choice,$flag){
$product_set = get_all_productTypes();
$totalProductTypes = mysql_num_rows($product_set);
$message = messages($flag);
echo "
<div class='msg'>{$message}</div>
<div id=myform>
	<div class='gama'>";
	while($product = mysql_fetch_assoc($product_set))
	{
		echo  "| ". $product['productType'] . " |";
	}
	echo "		
	</div>
	<hr>
	<form id='type' method='POST' action='create_process.php' name='create_type'>
	<div class='delta'>
		<label> product type * <input type='text' name='typeName' maxlength='30' required='required' autocomplete='on' autofocus='autofocus'/> </label>
		<input type='hidden' name='choice' value='{$choice}' id='choice'/><br>
		<button type='submit' id='create' name='submitType'>Create new type</button>
	</div>
	</form>
</div>
<div class='msg'>Total Product Types : $totalProductTypes</div>

<script>
$('#type').validator({ 
	position: 'top left', 
	offset: [-12, 0],
	message: '<div><em/></div>' 
});
</script>
";
}

/*--------------------------------------------------------------------
//This function will view all the types and option to delete
--------------------------------------------------------------------*/
function view_types($choice,$flag){
	$row_color = 1;
	$product_set = get_all_productTypes();
	$total_product = mysql_num_rows($product_set);
	$message = messages($flag);
	echo "
		<div class='extended_msg'>{$message}</div>
	";
	echo"
	<div class='type_view'>
		<table align='center' width='80%' cellspacing='3px' cellpadding='3px' border='0'>
			<tr class='tblhead'>
				<td>Serial</td>
				<td>Product Type</td>
				<td align='right'>Delete</td>
			</tr>
	";
	while($product = mysql_fetch_assoc($product_set)){
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
				<td>{$product['productType']}</td>
				<td align='right'><a href='settings.php?productId=".$product['id']."&choice=type_settings' class='{$btn}'>Delete</a></td>
			</tr>
		";
		$row_color++;
	}
	echo "
	</table>
	</div>
	<div class='extended_msg'>Total Products : $total_product</div>
	";
}

/*-------------------------------------------------------------------
//This function will delete types
-------------------------------------------------------------------*/
function delete_type($choice,$flag,$productId){
	$query = "SELECT * FROM producttypes WHERE id = '{$productId}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$products = mysql_fetch_assoc($result);
	$productType = $products['productType'];
	echo "
		<form id='myform' method='POST' action='update_process.php' name='delete_type'>
			<p> Do you really want to Delete the Product Type [ $productType ] ? If you want to continue on deleting the Product Type  [ $productType ] then press Delete user below or clcik Go back on the top right to go back to home page.</p>
			<br>
			<input type='hidden' name='choice' value='type_settings_view' id='choice'/>
			<input type='hidden' name='productId' value='{$productId}' id='productId'/>
			<input type='hidden' name='productType' value='{$productType}' id='productType'/>
			<button type='submit' id='create' name='deleteType'>Delete $productType</button>
		</form>
	";
}
/*------------------------------------------------------------------
//This function will create new record form
-------------------------------------------------------------------*/
function create_record($choice,$flag){
$product_set = get_all_productTypes();
$totalProducts = get_all_products();
$message = messages($flag);
echo "
<div class='msg'>{$message}</div>
<form id='myform' class='cols' method='POST' action='create_process.php' name='create_order'>
	<div class='alpha'>
			<label> product name * <input type='text' name='productName' maxlength='30' required='required' autocomplete='on' autofocus='autofocus'/> </label>
			<label> product type * <br>
				<select name='productType'>
				<option value='none'>--- none ---</option>
				";
					while($product = mysql_fetch_assoc($product_set))
					{
						echo "<option value='{$product['productType']}'>".$product['productType']."</option>";
					}
	echo "
				</select>
			</label>
			<label> product code * <input type='text' name='productCode' maxlength='15' required='required' autocomplete='on'/> </label>
	</div>
	<div class='beta'>
			<label> quantity * <input type='number' name='quantity' required='required' autocomplete='on'/> </label>
			<label> warranty (in months) * <input type='text' name='warranty' required='required' autocomplete='on'/> </label> 
			<label> unit price (in taka) * <input type='number' name='unitPrice'  required='required' autocomplete='on'/> </label>
			<input type='hidden' name='choice' value='{$choice}' id='choice'/><br>
	</div>

	<div class='clear'></div>

	<button type='submit' id='create' name='submitRecord'>Create new record</button>
</form>
<div class='msg'>Total Products : $totalProducts</div>
<script>
$('#myform').validator({ 
	position: 'top left', 
	offset: [-12, 0],
	message: '<div><em/></div>' 
});
</script>
";
}

/*---------------------------------------------------------------
//this function will get all the user and the total nuber
----------------------------------------------------------------*/
function get_all_the_users(){
	$query = "SELECT * FROM users WHERE status = 1";
	$user_set = mysql_query($query);
	confirm_query($user_set);
	$num_of_user = mysql_num_rows($user_set);
	return $num_of_user;
}

/*------------------------------------------------------------------
//This function will get all the product types
-------------------------------------------------------------------*/
function get_all_productTypes(){
	$query = "SELECT * FROM producttypes WHERE status = '1' ORDER BY productType ASC";
	$product_set = mysql_query($query);
	confirm_query($product_set);
	return $product_set;
}

/*------------------------------------------------------------------
//This function will get all the products
-------------------------------------------------------------------*/
function get_all_products(){
	$query = "SELECT * FROM products WHERE status = '1' ";
	$product_set = mysql_query($query);
	confirm_query($product_set);
	$no_of_products = mysql_num_rows($product_set);
	return $no_of_products;
}

/*------------------------------------------------------------------
//This function will get product by id
-------------------------------------------------------------------*/
function get_product($id){
	$query = "SELECT * FROM products WHERE id = '{$id}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$product = mysql_fetch_assoc($result);
	return $product;
}

/*------------------------------------------------------------------
//This function will get product id for the record
------------------------------------------------------------------*/
function get_product_id($productType){
	$prefix1 = "0000";
	$prefix2 = "000";
	$prefix3 = "00";
	$prefix4 = "0";

	$productTypePrefix = productType_prefix($productType);
	$productDate = date("dmY");

	$query = "SELECT * , count(*) FROM products WHERE productType = '{$productType}'  GROUP BY productType";
	$product_set = mysql_query($query);
	confirm_query($product_set);

	$product = mysql_fetch_assoc($product_set);
	$productId_prefix = $product['count(*)']+1;
	$length = strlen($productId_prefix);

	//echo $productType ;
	//echo "<br>pre: " . $prefix1;
	//echo "<br>Product ID: " . $productId_prefix;
	//echo "<br>length: " . $length;

	$productId_prefix_str = (string)$productId_prefix;

	//echo "<br>str: " . $productId_prefix_str ;
	//echo "<br>prefix: " . $productTypePrefix ."<br>";

	$productId1 =$prefix1.$productId_prefix . "-" . $productTypePrefix . "-" . $productDate ;
	$productId2 =$prefix2.$productId_prefix . "-" . $productTypePrefix . "-" . $productDate ;
	$productId3 =$prefix3.$productId_prefix . "-" . $productTypePrefix . "-" . $productDate ;
	$productId4 =$prefix4.$productId_prefix . "-" . $productTypePrefix . "-" . $productDate ;

	if($length==1){$new_productId = $productId1;}
	elseif($length==2){$new_productId = $productId2;}
	elseif($length==3){$new_productId = $productId3;}
	elseif($length==4){$new_productId = $productId4;}

	return $new_productId;
}

/*----------------------------------------------------------------
//This function will get the producttype prefix
-----------------------------------------------------------------*/
function productType_prefix($productType){
$prefix = trim(substr($productType,0,3));
$uppercase_prefix = strtoupper($prefix);
return $uppercase_prefix;
}

/*------------------------------------------------------------------
//This function will restrict unauthorized entry
-------------------------------------------------------------------*/
function access_denied(){
echo "
	<image src='../images/access_denied.gif'>
";
}

/*----------------------------------------------------------------
//This function will view the users and settings
----------------------------------------------------------------*/
function view_user($choice,$flag){
$user_set = get_all_users();
$totalUsers = mysql_num_rows($user_set);
$message = messages($flag);
echo "
<div class='extended_msg'>{$message}</div>
<ul>
";
while($user = mysql_fetch_assoc($user_set)){
	if($user['status']==1){$invert_status = "Deactivate";}
	elseif($user['status']==0){$invert_status = "Activate";}
	echo "
		<li>
			<span>[{$user['id']}] &nbsp;&nbsp; {$user['fullName']} &nbsp;&nbsp; [ {$user['accessCode']} ]</span>
			<div class='edit_delete'>";
			if($choice == "settings" || $choice == "ad" || $choice == "al" || $choice == "cp"){
			echo"
				<a href='settings.php?user=".$user['userName']."&choice=cp' class='link_btn1'>Change password</a>
				<a href='settings.php?user=".$user['userName']."&choice=al' class='link_btn2'>Change access level</a>
				<a href='settings.php?user=".$user['userName']."&choice=ad' class='link_btn1'>$invert_status</a>
			";
			}
			elseif($choice=="edit_delete" || $choice == "edit" || $choice == "delete"){
			echo "
				<a href='settings.php?user=".$user['userName']."&choice=edit' class='link_btn1'>Edit</a>
				<a href='settings.php?user=".$user['userName']."&choice=delete' class='link_btn2'>Delete</a>
			";
			}
			elseif($choice=="details"){
			echo "
				<a href='settings.php?user=".$user['userName']."&choice=logs' class='link_btn1'>Logs</a>
				<a href='settings.php?user=".$user['userName']."&choice=details' class='link_btn2'>Details</a>
			";
			}
			elseif($choice=="logs"){
			echo "
				<a href='settings.php?user=".$user['userName']."&choice=logs' class='link_btn2'>Logs</a>
			";
			}
			else{
			echo "
				<a href='settings.php?user=".$user['userName']."&choice=details' class='link_btn1'>Details</a>
			";
			}
	echo"	
		</div>
		</li>
	";
}
echo "
</ul>
<div class='extended_msg'>Total Users : $totalUsers</div>
";
}

/*---------------------------------------------------------------
//This function will get all the users details in a set
----------------------------------------------------------------*/
function get_all_users(){
$query = "SELECT * FROM users";
$user_set = mysql_query($query);
confirm_query($user_set);
return $user_set;
}

/*------------------------------------------------------------
//This function will activate and deactivate users
------------------------------------------------------------*/
function ad_user($choice,$flag,$user){
	$query = "SELECT * FROM users WHERE userName = '{$user}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$users = mysql_fetch_assoc($result);
	$status = $users['status'];
	$accessCode = $users['accessCode'];
	update_status($status,$user,$choice,$accessCode);
}

/*------------------------------------------------------------
//This function will change the access lavel of user
------------------------------------------------------------*/
function al_user($choice,$flag,$user){
	$query = "SELECT * FROM users WHERE userName = '{$user}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$users = mysql_fetch_assoc($result);
	$accessCode = $users['accessCode'];
	if($accessCode == "green")
	{
		header("Location:user_view.php?flag=theta&choice={$choice}");
		exit;
	}
	else
	{
		echo"
			<form id='myform' method='POST' action='settings_process.php' name='change_level'>
				<div class='epsilon'>
					<p>The user&nbsp;$user&nbsp;is now have an access level code &nbsp;" . strtoupper($accessCode) . "</p><br>
					<label> Change access level to *
						<select name='accessCode'>";
							  if($accessCode=="yellow"){
							  echo"
								<option value='green'>green</option>
								<option value='red'>red</option> ";
							  }
							  elseif($accessCode="red"){
							  echo"
								<option value='green'>green</option>
								<option value='yellow'>yellow</option> ";
							  }
						echo"
						</select>
					</label>
					<input type='hidden' name='user' value='{$user}' id='user'/><br>
					<input type='hidden' name='choice' value='{$choice}' id='choice'/><br>
				</div>
				<button type='submit' id='create' name='submitLevel'>Change access level</button>
			</form>
			<script>
			$('#myform').validator({ 
				position: 'top left', 
				offset: [-12, 0],
				message: '<div><em/></div>' 
			});
			</script>
			";
	}
}

/*------------------------------------------------------------
//This function will change the password of user
------------------------------------------------------------*/
function cp_user($choice,$flag,$user){
echo"
	<form id='myform' method='POST' action='settings_process.php' name='change_password'>
		<div class='epsilon'>
			<label> Password * <input type='password' name='password' minlength='6' required='required'/> </label> 
			<label> Password check <input type='password' name='check' data-equals='password' /> </label>
			<input type='hidden' name='user' value='{$user}' id='user'/><br>
			<input type='hidden' name='choice' value='{$choice}' id='choice'/><br>
		</div>
		<button type='submit' id='create' name='submitPassword'>Change password</button>
	</form>
	<script> 
		$.tools.validator.fn('[data-equals]', 'Value not equal with the $1 field', function(input) {
		var name = input.attr('data-equals'),
		field = this.getInputs().filter('[name=' + name + ']'); 
		return input.val() == field.val() ? true : [name]; 
		});

		$.tools.validator.fn('[minlength]', function(input, value) {
		var min = input.attr('minlength');

		return value.length >= min ? true : {     
		en: 'Please provide at least ' +min+ ' character' + (min > 1 ? 's' : ''),
		fi: 'Kentän minimipituus on ' +min+ ' merkkiä' 
		};
		});

		$('#myform').validator({ 
		position: 'top left', 
		offset: [-12, 0],
		message: '<div><em/></div>' 
		});
	</script>
";
}

/*-----------------------------------------------------------
//This function will update the status of the user 
------------------------------------------------------------*/
function update_status($status,$user,$choice,$accessCode){
	if($accessCode != "green"){
		if($status==1){$status_query = "UPDATE users SET status = 0 WHERE userName='$user' ";}
		if($status==0){$status_query = "UPDATE users SET status = 1 WHERE userName='$user' ";}
		$status_result = mysql_query($status_query);
		if($status_result)
		{
			header("Location:user_view.php?flag=epsilon&choice={$choice}");
			exit;
		}
		else
		{
			header("Location:user_view.php?flag=delta&choice={$choice}");
			exit;
		}
	}
	else{
		header("Location:user_view.php?flag=chi&choice={$choice}");
		exit;
	}
}

/*-------------------------------------------------------------
//This function will edit the user details
--------------------------------------------------------------*/
function edit_user($choice,$flag,$user){
	$query = "SELECT * FROM users WHERE userName = '{$user}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$users = mysql_fetch_assoc($result);
echo"
	<form id='myform' class='cols' method='POST' action='update_process.php' name='update_user'>
		<div class='alpha'>
				<label> full name * <input type='text' name='fullName' maxlength='30' required='required' autocomplete='on' value='".$users['fullName']."'/> </label>
				<label> address * <input type='text' name='address' maxlength='60' required='required' autocomplete='on' value='".$users['address']."'/> </label>
				<label> email * <input type='email' required='required' minlength='9' name='eMail' autocomplete='on' value='".$users['eMail']."'/> </label> 
				<label> display name * <input type='text' name='displayName' maxlength='16' required='required' autocomplete='on' value='".$users['displayName']."'/> </label>
		</div>
		<div class='beta'>
				<label> cell no * <input type='number' name='cellNo' maxlength='16' required='required' autocomplete='on' value='".$users['cellNo']."'/> </label>
				<label> username * <input type='text' name='userName' minlength='6' required='required' autocomplete='on' readonly='readonly' value='".$users['userName']."'/> </label>
				<input type='hidden' name='choice' value='{$choice}' id='choice'/>
				<label> access code * <input type='text' name='accessCode' required='required' autocomplete='on' readonly='readonly' value='".$users['accessCode']."'/> </label>
		</div>

		<div class='clear'></div>
		
		<button type='submit' id='create' name='updateUser'>Update user</button>
	</form>

	<script> 
	$.tools.validator.fn('[data-equals]', 'Value not equal with the $1 field', function(input) {
		var name = input.attr('data-equals'),
			 field = this.getInputs().filter('[name=' + name + ']'); 
		return input.val() == field.val() ? true : [name]; 
	});

	$.tools.validator.fn('[minlength]', function(input, value) {
		var min = input.attr('minlength');
		
		return value.length >= min ? true : {     
			en: 'Please provide at least ' +min+ ' character' + (min > 1 ? 's' : ''),
			fi: 'Kentän minimipituus on ' +min+ ' merkkiä' 
		};
	});

	$('#myform').validator({ 
		position: 'top left', 
		offset: [-12, 0],
		message: '<div><em/></div>' 
	});
	</script>
";
}

/*----------------------------------------------------------------
//This function will view the details of the user
-----------------------------------------------------------------*/
function view_details($choice,$flag,$user){
	$query = "SELECT * FROM users WHERE userName = '{$user}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$users = mysql_fetch_assoc($result);
	$user_since =date("d.m.Y H:i:s",$users['creationDate']);
	$last_login = date("d.m.Y H:i:s",$users['lastLogin']);
	if($users['status'] == 1){ $userStatus = "active"; }
	else{ $userStatus == "inactive"; }
if($user == $_SESSION['USERNAME'] || $_SESSION['ACCESSCODE']=="green"){
echo"
	<div id='myform' class='cols'>
		<div class='alpha'>
				<label> user id <input type='text' name='userid' required='required' readonly='readonly' autocomplete='on' value='".$users['id']."'/> </label>
				<label> full name <input type='text' name='fullName' maxlength='30' required='required' readonly='readonly' autocomplete='on' value='".$users['fullName']."'/> </label>
				<label> address <input type='text' name='address' required='required' readonly='readonly' autocomplete='on' value='".$users['address']."'/> </label>
				<label> email <input type='email' required='required' minlength='9' name='eMail' readonly='readonly' autocomplete='on' value='".$users['eMail']."'/> </label> 
				<label> display name <input type='text' name='displayName' maxlength='16' required='required' readonly='readonly' autocomplete='on' value='".$users['displayName']."'/> </label>
				<label> cell no <input type='number' name='cellNo' maxlength='16' required='required' readonly='readonly' autocomplete='on' value='".$users['cellNo']."'/> </label>
		</div>
		<div class='beta'>
				<label> username <input type='text' name='userName' minlength='6' required='required' autocomplete='on' readonly='readonly' value='".$users['userName']."'/> </label>
				<label> access code <input type='text' name='accessCode' required='required' autocomplete='on' readonly='readonly' value='".$users['accessCode']."'/> </label>
				<label> user since <input type='text' name='creationDate' required='required' readonly='readonly' autocomplete='on' value='".$user_since."'/> </label>
				<label> last login <input type='text' name='creationDate' required='required' readonly='readonly' autocomplete='on' value='".$last_login."'/> </label>
				<label> total sale <input type='text' name='accessCode' required='required' autocomplete='on' readonly='readonly' value='".$users['totalSale']."'/> </label>
				<label> user status <input type='text' name='creationDate' required='required' readonly='readonly' autocomplete='on' value='".$userStatus."'/> </label>
		</div>

		<div class='clear'></div>
		
	</div>
";
}
else{
access_denied();
}
}

/*-----------------------------------------------------
//This function will dlete the specific user
-----------------------------------------------------*/
function delete_user($choice,$flag,$user){
	$query = "SELECT * FROM users WHERE userName = '{$user}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$users = mysql_fetch_assoc($result);
	$accessCode = $users['accessCode'];
	if($accessCode == "green")
	{
		header("Location:user_view.php?flag=iota&choice={$choice}");
		exit;
	}
	else{
		echo "
			<form id='myform' method='POST' action='update_process.php' name='delete_user'>
				<p> Do you really want to Delete the user [ $user ] ? If you want to continue on deleting the user  [ $user ] then press Delete user below or clcik Go back on the top right to go back to home page.</p>
				<br>
				<input type='hidden' name='choice' value='{$choice}' id='choice'/>
				<input type='hidden' name='user' value='{$user}' id='user'/>
				<button type='submit' id='create' name='deleteUser'>Delete $user</button>
			</form>
		";
	}
}

/*------------------------------------------------
//This function will show us the user logs
-------------------------------------------------*/
function user_logs($choice,$flag,$user){
	$query = "SELECT * FROM accesslogs WHERE userName='{$user}' ORDER BY id DESC";
	$result_set = mysql_query($query);
	confirm_query($result_set);
	$total_logs = mysql_num_rows($result_set);
	echo "
		<ul>";
			while($logs = mysql_fetch_assoc($result_set)){
			$type = $logs['type'];
			if($type=="login"){$wr_type = "Logged In";}
			elseif($type=="logout"){$wr_type = "Logged Out";}
			$time = date("d-m-Y H:i:s",$logs['time']);
			echo"
			<li><span>[{$logs['id']}] &nbsp;&nbsp;{$logs['userName']}&nbsp;has&nbsp;[{$wr_type}]&nbsp;at&nbsp;[{$time}]&nbsp;from&nbsp;IP&nbsp;[{$logs['ipAddress']}]</span></li>
			";
			}
	echo "
		</ul>
		<div class='extended_msg'>Total Logs : {$total_logs}</div>
	";
}

/*------------------------------------------------------------------
//This function will select a type and show the inventory
------------------------------------------------------------------*/
function select_inventory($choice,$flag,$accessCode){
	if($accessCode != "red"){
		$product_set = get_all_productTypes();
		//$totalProducts = get_all_products_by_type();
		$message = messages($flag);
		echo "
		<div class='extended_msg'>{$message}</div>
		<form id='displayBack' method='POST' action='inventory_view.php' name='show_record'>
		<p>Please select a type to show/edit the inventory</p>
			<div class='displayAlpha'>
					<input type='hidden' name='choice' value='{$choice}' id='choice'/>
					<label> product type * <br>
						<select name='productType'>
						<option value='none'>--- none ---</option>
						";
							while($product = mysql_fetch_assoc($product_set))
							{
								echo "<option value='{$product['productType']}'>".$product['productType']."</option>";
							}
			echo "
						</select>
				</div>
			<div class='clear'></div>
			
			<button type='submit' id='create' name='showRecord'>View Inventory</button>
			
			</form>
			";
	}
	else{
		access_denied();
	}
}

/*-------------------------------------------------------
//This function will show and hide the products
-------------------------------------------------------*/
function sh_product($choice,$flag,$productId){
	$query = "SELECT * FROM products WHERE productId = '{$productId}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$products = mysql_fetch_assoc($result);
	$status = $products['status'];
	update_product_status($status,$productId,$choice);
}

/*-----------------------------------------------------------
//This function will update the status of the product 
------------------------------------------------------------*/
function update_product_status($status,$productId,$choice){
	if($status==1){$status_query = "UPDATE products SET status = 0 WHERE productId='$productId' ";}
	if($status==0){$status_query = "UPDATE products SET status = 1 WHERE productId='$productId' ";}
	$status_result = mysql_query($status_query);
	if($status_result)
	{
		header("Location:inventory_view.php?flag=epsilon&choice={$choice}");
		exit;
	}
	else
	{
		header("Location:inventory_view.php?flag=delta&choice={$choice}");
		exit;
	}
}

/*------------------------------------------------------------
//This function will edit the products
------------------------------------------------------------*/
function edit_product($choice,$flag,$productId){
$query = "SELECT * FROM products WHERE productId = '{$productId}' ";
$result = mysql_query($query);
confirm_query($result);
$products = mysql_fetch_assoc($result);
	echo "
		<form id='myform' class='cols' method='POST' action='update_process.php' name='create_order'>
		<div class='alpha'>
				<label> product name * <input type='text' name='productName' required='required' autocomplete='on' autofocus='autofocus' value='".$products['productName']."'/> </label>
				<label> product code * <input type='text' name='productCode' required='required' autocomplete='on' value='".$products['productCode']."'/> </label>
				<input type='hidden' name='productId' value='{$productId}' id='productId'/>
		</div>
		<div class='beta'>
				<label> quantity * <input type='number' name='quantity' required='required' autocomplete='on' value='".$products['quantity']."'/> </label>
				<label> warranty (in years) * <input type='text' name='warranty' required='required' autocomplete='on' value='".$products['warranty']."'/> </label> 
				<label> unit price (in taka) * <input type='number' name='unitPrice'  required='required' autocomplete='on' value='".$products['unitPrice']."'/> </label>
				<input type='hidden' name='choice' value='{$choice}' id='choice'/>
		</div>

		<div class='clear'></div>

		<button type='submit' id='create' name='updateRecord'>Update record</button>
		</form>
		<script>
		$('#myform').validator({ 
		position: 'top left', 
		offset: [-12, 0],
		message: '<div><em/></div>' 
		});
		</script>
	";
}

/*-------------------------------------------------------------
//This function will delete a record only if it's empty
-------------------------------------------------------------*/
function delete_product($choice,$flag,$productId){
	$query = "SELECT * FROM products WHERE productId = '{$productId}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$products = mysql_fetch_assoc($result);
	$quantity = $products['quantity'];
	if($quantity != 1)
	{
		header("Location:inventory_view.php?flag=mu&choice={$choice}");
		exit;
	}
	else{
		echo "
			<form id='myform' method='POST' action='update_process.php' name='delete_product'>
				<p> Do you really want to Delete the product [ $productId ] ? If you want to continue on deleting the product then press Delete below or clcik Go back on the top right to go back to home page.</p>
				<br>
				<input type='hidden' name='choice' value='{$choice}' id='choice'/>
				<input type='hidden' name='productId' value='{$productId}' id='productId'/>
				<button type='submit' id='create' name='deleteRecord'>Delete $productId</button>
			</form>
		";
	}
}

/*------------------------------------------------------------------
//This function will select a date and show the inventory
------------------------------------------------------------------*/
function search_inventory($choice,$flag,$accessCode){
	if($accessCode == "green"){
		$product_set = get_all_productTypes();
		$message = messages($flag);
		echo "
		<div class='extended_msg'>{$message}</div>
		<form id='displayBack' method='POST' action='search_view.php' name='search_record'>
		<p>Please select a date (dd-mm-yyyy) to show/edit the inventory</p>
			<div class='displayAlpha'>
					<input type='hidden' name='choice' value='{$choice}' id='choice'/>";
					echo "
					<select name='day'>
					<option value='none'>--- day ---</option>
					";
					for($day=1; $day<=31;$day++){
						echo "<option value='{$day}'>".$day."</option>";
					}
					echo"
					</select>
					<select name='month'>
					<option value='none'>--- month ---</option>
					";
					for($month=1; $month<=12;$month++){
						echo "<option value='{$month}'>".$month."</option>";
					}
					echo"
					</select>
					<select name='year'>
					<option value='none'>--- year ---</option>
					";
					for($year=2010; $year<=2050;$year++){
						echo "<option value='{$year}'>".$year."</option>";
					}
			echo"
			</select>
			</div>
			<div class='clear'></div>
			
			<button type='submit' id='create' name='searchRecord'>Search Inventory</button>
			
			</form>
			";
	}
	else{
		access_denied();
	}
}

/*-------------------------------------------------------------------------
//This function will select a date and show the sales inventory
--------------------------------------------------------------------------*/
function search_salesInventory($flag,$accessCode){
	if($accessCode == "green"){
		$message = messages($flag);
		echo "
		<div class='extended_msg'>{$message}</div>
		<form id='displayBack' method='POST' action='sales_by_date.php' name='search_record'>
		<p>Please select a date (dd-mm-yyyy) to show the orders of the day</p>
			<div class='displayAlpha'>";
					echo "
					<select name='day'>
					<option value='none'>--- day ---</option>
					";
					for($day=1; $day<=31;$day++){
						echo "<option value='{$day}'>".$day."</option>";
					}
					echo"
					</select>
					<select name='month'>
					<option value='none'>--- month ---</option>
					";
					for($month=1; $month<=12;$month++){
						echo "<option value='{$month}'>".$month."</option>";
					}
					echo"
					</select>
					<select name='year'>
					<option value='none'>--- year ---</option>
					";
					for($year=2010; $year<=2050;$year++){
						echo "<option value='{$year}'>".$year."</option>";
					}
			echo"
			</select>
			</div>
			<div class='clear'></div>
			
			<button type='submit' id='create' name='searchSales'>Search Sales Inventory</button>
			
			</form>
			";
	}
	else{
		access_denied();
	}
}

/*-------------------------------------------------------------------------------------
//This function will view the sales reports by ID and Cell No
---------------------------------------------------------------------------------------*/
function searchSales_by_id($flag){
	if($_SESSION['LOGGEDIN']==TRUE){
		$message = messages($flag);
		echo "
			<div class='extended_msg'>{$message}</div>
			<form id='displayBack' method='POST' action='sales_by_id.php'>
				<p>Please select a type and write the ID or Cell No to show the orders</p>
				<div class='displayAlpha'>";
		echo "
				<select name='searchType'>
					<option value='none'>---- Select ----</option>
					<option value='customerCell'>Cell No</option>
					<option value='bill_id'>Bill ID</option>
				</select>";
		echo "
				<input type='text' name='searchInput' size='20'/>
				";
		echo "
				</div>
				<div class='clear'></div>
				<button type='submit' name='searchSales_byid'>Search Sales Inventory</button>
			</form>
		";
	}
	else{
		access_denied();
	}
}

/*--------------------------------------------------------------------------------------
//This function will select a type and show the inventory for adding to cart
---------------------------------------------------------------------------------------*/
function select_cart($choice,$flag,$accessCode){
	if($choice == 'select_cart' || $choice == 'add_product'){
		$product_set = get_all_productTypes();
		$message = messages($flag);
		if($message)
		{
			echo "
				<div class='extended_msg'>{$message}</div>
			";
		}
		echo"
			<form id='displayBack' method='POST' action='create_order.php' name='show_record'>
			<p>Please select a type to show the inventory for adding to the cart</p>
				<div class='displayAlpha'>
						<input type='hidden' name='choice' value='{$choice}' id='choice'/>
						<label> product type * <br>
							<select name='productType'>
							<option value='none'>--- none ---</option>
							";
								while($product = mysql_fetch_assoc($product_set))
								{
									echo "<option value='{$product['productType']}'>".$product['productType']."</option>";
								}
				echo "
							</select>
					</div>
				<div class='clear'></div>
				
				<button type='submit' id='create' name='showRecord'>View Inventory</button>
				
				</form>
			";
	}
	else{
		access_denied();
	}
}

/*------------------------------------------------------------------
//This function will add the item to the cart
------------------------------------------------------------------*/
function add_to_cart($pid,$qty){
	if($pid<1 or $qty<1) return;
	$unitPrice = get_unitPrice($pid);
	if(is_array($_SESSION['cart'])){
		if(product_exists($pid)) return;
		$max=count($_SESSION['cart']);
		$_SESSION['cart'][$max]['productid']=$pid;
		$_SESSION['cart'][$max]['qty']=$qty;
		$_SESSION['cart'][$max]['unitPrice']= $unitPrice;
	}
	else{
		$_SESSION['cart']=array();
		$_SESSION['cart'][0]['productid']=$pid;
		$_SESSION['cart'][0]['qty']=$qty;
		$_SESSION['cart'][0]['unitPrice']= $unitPrice;
	}
}

/*------------------------------------------------------------------
//This function will delete item from the cart
------------------------------------------------------------------*/
function remove_product($pid){
	$pid=intval($pid);
	$max=count($_SESSION['cart']);
	for($i=0;$i<$max;$i++){
		if($pid==$_SESSION['cart'][$i]['productid']){
			unset($_SESSION['cart'][$i]);
			break;
		}
	}
	$_SESSION['cart']=array_values($_SESSION['cart']);
}
/*------------------------------------------------------------------
//This function will check for duplicate items in the cart
------------------------------------------------------------------*/
function product_exists($pid){
	$pid=intval($pid);
	$max=count($_SESSION['cart']);
	$flag=0;
	for($i=0;$i<$max;$i++){
		if($pid==$_SESSION['cart'][$i]['productid']){
			$flag=1;
			break;
		}
	}
	return $flag;
}

/*------------------------------------------------------------------
//This function will check for duplicate items in the cart
------------------------------------------------------------------*/
function get_unitPrice($pid){
	$query = "SELECT unitPrice FROM products WHERE products.id = '$pid' ";
	$result = mysql_query($query);
	confirm_query($result);
	$item_price = mysql_result($result, 0, 'unitPrice');
	return $item_price;
}
/*-------------------------------------------------------------
//This function will get the qty of the product
-------------------------------------------------------------*/
function get_dbqty($pid){
	$query = "SELECT quantity FROM products WHERE products.id = '$pid' ";
	$result = mysql_query($query);
	confirm_query($result);
	$item_qty = mysql_result($result, 0, 'quantity');
	return $item_qty;
}
/*--------------------------------------------------------------
//This function will find out the total items
---------------------------------------------------------------*/
function total_items(){
	$max=count($_SESSION['cart']);
	$num_items = 0;
	for($i=0;$i<$max;$i++){
		$qty=$_SESSION['cart'][$i]['qty'];
		$num_items += $qty;
	}
	return $num_items;
}

/*--------------------------------------------------------------
//This function will find out the total price
---------------------------------------------------------------*/
function total_price(){
	$max=count($_SESSION['cart']);
	$sum=0.00;
	for($i=0;$i<$max;$i++){
		$qty=$_SESSION['cart'][$i]['qty'];
		$price=$_SESSION['cart'][$i]['unitPrice'];
		$sum+=$price*$qty;
	}
	return $sum;
}

/*--------------------------------------------------------
//This function will show the cart
---------------------------------------------------------*/
function show_cart($view){
	$_SESSION['time'] = date("H:i:s");
	if($view =="confirm_cart"){
		echo"
		<div id = 'title'>
			<h1>Power IT Computer</h1>
			<h4>Show Room: Shop No: 226, Alaka Nodi Bangla Shopping Center (1st Floor), Mymensingh</h4>
			<h4>Head Office: 51, Jail Road, Mymensingh</h4>
			<h4>Cell No: 01716 - 36 42 00 &nbsp; OR &nbsp; 01675 - 30 11 61</h4>
			<h4>E-mail: info@powertitbd.com</h4>
			<hr>
		</div>
		";
	}
	echo "
	<div id='print'>
	<form method='POST' action='cart.php'>
		<fieldset class='fldst'>
			<legend>Customer Details</legend>
			<table align='center' width='98%'  cellspacing='5' cellpadding='5' border='0'>
				<tr>
					<td>Bill ID</td>
					<td>Date</td>
					<td>Time</td>
					<td>Prepared By</td>
				</tr>
				<tr>
					<td>{$_SESSION['bill_id']}</td>
					<td>{$_SESSION['date']}</td>
					<td>{$_SESSION['time']}</td>
					<td>{$_SESSION['USERNAME']}</td>
				</tr>
				<tr><td colspan='4'><br></td></tr>
				<tr>
					<td>Customer Name *</td>
					<td>Customer Address *</td>
					<td>Customer Cell No *</td>
					<td>Customer E-mail</td>
				</tr>";
				if($view == "cart"){
					echo"
					<tr>
						<td><input type='text' maxlength='40' required='required' autofocus='autofocus' autocomplete='on' name='customerName' value='{$_SESSION['customer_name']}'/></td>
						<td><input type='text' maxlength='80' required='required' autocomplete='on' name='customerAddress' value='{$_SESSION['customer_address']}'/></td>
						<td><input type='number' maxlength='16' required='required' autocomplete='on' name='customerCell' value='{$_SESSION['customer_cell']}'/></td>
						<td><input type='email' maxlength='60' autocomplete='on' name='customerEmail' value='{$_SESSION['customer_email']}'/></td>
					</tr>
					";
				}
				if($view =="confirm_cart"){
					echo"
						<tr>
							<td>{$_SESSION['customer_name']}</td>
							<td>{$_SESSION['customer_address']}</td>
							<td>{$_SESSION['customer_cell']}</td>
							<td>{$_SESSION['customer_email']}</td>
						</tr>
					";
				}
				echo"
				<tr><td colspan='4'><br></td></tr>
				";
				if($view == "cart"){
					echo"
						<tr><td colspan='4' align='right'><input type='submit' name='submitCustomer' class='submitcart' value='Update Customer'/></td></tr>
					";
				}
			echo"
			</table>
		</fieldset>
		</form>
		";
		
	echo"
	<form method='POST' action='cart.php'>
		<fieldset class='fldst'>
		<legend>Cart Details</legend>
		<table align='center' width='98%' class='tbl' cellspacing='5' cellpadding='5'>
			<tr class='tblhead'>
				<td align='left'>Serial</td>
				<td align='left'>Product Name</td>
				<td align='left'>Product Type</td>
				<td align='left'>Product Code</td>
				<td>Warranty</td>
				<td>Quantity</td>
				<td align='right'>Unit Price</td>
				<td align='right'>Subtotal</td>
			</tr>
			<tr><td><br></td></tr>
	";
	if(is_array($_SESSION['cart'])){
		$max=count($_SESSION['cart']);
		for($i=0;$i<$max;$i++){
			$pid=$_SESSION['cart'][$i]['productid'];
			$qty=$_SESSION['cart'][$i]['qty'];
			$item_price=$_SESSION['cart'][$i]['unitPrice'];
			$unitPrice=number_format($item_price,2); 
			$product = get_product($pid);
			$subTotal = number_format(($item_price * $qty),2) ;
			$totalPrice = number_format($_SESSION['total_price'],2);
			$serial = $i +1;
			if($i % 2==0){
				$tbl_class = "tbldata_even";
			}
			else{
				$tbl_class="tbldata_odd";
			}
			echo"
			<tr class='{$tbl_class}'>
				<td align='left'>{$serial}</td>
				<td align='left'>{$product['productName']}</td>
				<td align='left'>{$product['productType']}</td>
				<td align='left'>{$product['productCode']}</td>
				<td>{$product['warranty']}</td>
				";
				if($view=="cart"){
					echo"
						<td><input type='text' size='2' maxlength='3' name ='qty".$pid."' value='{$qty}' class='price_box'/></td>
						<td><input type='text' size='10' name ='unt".$pid."' value='{$item_price}' class='price_box'/></td>
					";
				}
				if($view=="confirm_cart"){
					echo"
						<td align='center'>{$qty}</td>
						<td align='right'>{$unitPrice}</td>
					";
				}
				echo"
				<td align='right'>{$subTotal}</td>
			";
			echo"
			</tr>
			";
		}
	}
	echo "
		<tr><td><br></td></tr>";
	if($view == "confirm_cart"){
		echo"
			<tr>
				<td colspan='7' align='right'>Installation / Service Charge</td>
				<td align='right'>0.00</td>
			</tr>
			<tr>
				<td colspan='7' align='right'>Value Added Tax</td>
				<td align='right'>0.00</td>
			</tr>
			<tr>
				<td align='left'>Total Paid</td>
				<td align='left' class='total'>{$totalPrice}</td>
				<td colspan='5' align='right'>Discount</td>
				<td align='right'>0.00</td>
			</tr>
			<tr>
				<td colspan='8'><br></td>
			</tr>
		";
	}
	echo"
		<tr>";
			if($view == "confirm_cart"){
			$cols = 5;
			echo"
				<td align='left'>Due</td>
				<td class='total' align='left'>0.00</td>
			";
			}
			else{
				$cols = 7;
			}
		echo"
			<td colspan='{$cols}' align='right'>Grand Total</td>
			<td align='right' class='total'>{$totalPrice}</td>
		</tr>
		<tr><td><br></td></tr>
		";
		if($view=="cart"){
			echo"
				<tr><td colspan='8' align='right'><input type='submit' name='submitCart' class='submitcart' value='Update Cart'/></td></tr>
			";
		}
		echo"
		</table>
		</fieldset>
	</form>
	";
	if($view == "confirm_cart"){
		echo "
			<div id='sign'>
				<div class='cust_sign'>
					<hr>
					Customer's Signature
				</div>
				<div class='auth_sign'>
					<hr>
					Authorized Signature
				</div>
			</div>
			<br><br>
			<div class='nb'>
				<p>Notes: </p>
				<p>#1 : Warranty Will be void if sticker removed, Physically damaged and burn case. All kinds of valid warranty is measured in Months.</p>
				<p>#2 : No Warranty for - Keyboard, Mouse, Power Supply, Speaker, Headphone, Cartridge-Toner, Casing, Webcam, Digital Camera, Card Reader, Bluetooth, Hub, Remote Control, Adapter etc.</p>
				<p>#3 : In any case of sales return/servicing or other problem, you <b>MUST</b> bring the copy of Invoice/bill/payment receipt.</p>
				<p>#4 : VAT (Value Added Tax) is not included.</p>
			</div>
		";
	}
	if($view =="cart"){
		echo "
		<div class='extended_msg'>
			<a href='home.php?choice=cart_finish' class='link_btn'>Cancel Order</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href='create_order.php?choice=select_cart' class='link_btn'>Continue Order</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href='confirm_order.php' class='link_btn'>Confirm Order</a>
		</div>";
	}
	if($view=="confirm_cart"){
		echo "
		<div class='extended_msg'>
			<a href='javascript:window.print()' class='link_btn'>Print</a>
			<a href='sales_settings.php?bill=".$_SESSION['bill_id']."&choice=due_bill' class='link_btn'>Make Due Invoice</a>
			<a href='home.php?choice=cart_finish' class='link_btn'>Finish</a>
		</div>";	
	}
	echo"
		</div>
	";
}

/*-----------------------------------------------------------------
//This function will deduct the quantity of the products
-----------------------------------------------------------------*/
function deduct_qty($id,$qty){
	$products = get_product($id);
	$oldQuantity = $products['quantity'];
	$newQuantity = $oldQuantity - $qty;
	if($oldQuantity >= $qty){
		$query = "UPDATE products SET quantity = '{$newQuantity}' WHERE id = '{$id}' ";
		$result = mysql_query($query);
		confirm_query($result);
		if($result){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	else{
		return TRUE;
	}
}

/*---------------------------------------------------------
//This function adds a sale to the user
----------------------------------------------------------*/
function increase_sale($userName){
	$users = get_user_by_userName($userName);
	$oldTotalSale = $users['totalSale'];
	$newTotalSale = $oldTotalSale + 1;
	$query = "UPDATE users SET totalSale = '{$newTotalSale}' WHERE userName = '{$userName}' ";
	$result = mysql_query($query);
	confirm_query($result);
	if($result){
		return TRUE;
	}
	else{
		return FALSE;
	}
}

/*-----------------------------------------------------------
//This function will get the user by it's userName
-----------------------------------------------------------*/
function get_user_by_userName($userName){
	$query = "SELECT * FROM users WHERE userName = '{$userName}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$users = mysql_fetch_assoc($result);
	return $users;
}
/*------------------------------------------------------
//This function will get the bill id
------------------------------------------------------*/
function get_bill_id(){
	$prefix = date("dm");
	$dot = ".";
	$suffix = "7860";
	$id = get_num_of_bills();
	$bill_id = $prefix . $dot . $suffix . $id;
	return $bill_id;
}

/*----------------------------------------------------
//This function will get the number of bills
-----------------------------------------------------*/
function get_num_of_bills(){
	$query = "SELECT * FROM customers";
	$result = mysql_query($query);
	confirm_query($result);
	$num_of_bills_db = mysql_num_rows($result);
	$num_of_bills = $num_of_bills_db + 1 ;
	return $num_of_bills;
}

/*------------------------------------------------------
//This function will get the payment id
------------------------------------------------------*/
function get_payment_id(){
	$prefix = date("dm");
	$dot = ".";
	$suffix = "1810";
	$id = get_num_of_payments();
	$payment_id = $prefix . $dot . $suffix . $id;
	return $payment_id;
}

/*----------------------------------------------------
//This function will get the number of bills
-----------------------------------------------------*/
function get_num_of_payments(){
	$query = "SELECT * FROM payments";
	$result = mysql_query($query);
	confirm_query($result);
	$num_of_payments_db = mysql_num_rows($result);
	$num_of_payments = $num_of_payments_db + 1 ;
	return $num_of_payments;
}
/*-----------------------------------------------------------
//This function will view the bill by the bill ID
-----------------------------------------------------------*/
function view_bill($bill_id){
	$row_color = 1;
	$customers = get_customer_details($bill_id);
	$grandTotal = number_format($customers['grandTotal'],2);
	echo"
		<div id = 'title'>
			<h1>Power IT Computer</h1>
			<h4>Show Room: Shop No: 226, Alaka Nodi Bangla Shopping Center (1st Floor), Mymensingh</h4>
			<h4>Head Office: 51, Jail Road, Mymensingh</h4>
			<h4>Cell No: 01716 - 36 42 00 &nbsp; OR &nbsp; 01675 - 30 11 61</h4>
			<h4>E-mail: info@powertitbd.com</h4>
			<hr>
		</div>
	";
	echo "
	<div id='print'>
		<fieldset class='fldst'>
			<legend>Customer Details</legend>
			<table align='center' width='98%'  cellspacing='5' cellpadding='5' border='0'>
				<tr>
					<td>Bill ID</td>
					<td>Date</td>
					<td>Time</td>
					<td>Prepared By</td>
				</tr>
				<tr>
					<td>{$bill_id}</td>
					<td>{$customers['billDate']}</td>
					<td>{$customers['billTime']}</td>
					<td>{$customers['preparedBy']}</td>
				</tr>
				<tr><td colspan='4'><br></td></tr>
				<tr>
					<td>Customer Name</td>
					<td>Customer Address</td>
					<td>Customer Cell No</td>
					<td>Customer E-mail</td>
				</tr>
				<tr>
					<td>{$customers['customerName']}</td>
					<td>{$customers['customerAddress']}</td>
					<td>{$customers['customerCell']}</td>
					<td>{$customers['customerEmail']}</td>
				</tr>
				<tr><td colspan='4'><br></td></tr>
			</table>
		</fieldset>
		";
		$due = number_format(($customers['grandTotal']-$customers['totalPaid']),2);
		$totalPaid = number_format($customers['totalPaid'],2);
		$product_set = get_sales_product_details($bill_id);
		
	echo"
		<fieldset class='fldst'>
		<legend>Cart Details</legend>
		<table align='center' width='98%' class='tbl' cellspacing='5' cellpadding='5' border='0'>
			<tr class='tblhead'>
				<td align='left'>Serial</td>
				<td align='left'>Product Name</td>
				<td align='left'>Product Type</td>
				<td align='left'>Product Code</td>
				<td>Warranty</td>
				<td>Quantity</td>
				<td align='right'>Unit Price</td>
				<td align='right'>Subtotal</td>
			</tr>
			<tr><td><br></td></tr>
	";
	while($products = mysql_fetch_assoc($product_set)){
		if($row_color % 2==0){
			$tbl_class = "tbldata_even";
		}
		else{
			$tbl_class="tbldata_odd";
		}
		$unitPrice = number_format($products['unitPrice'],2);
		$subTotal = number_format($products['subTotal'],2);
		echo"
		<tr class='{$tbl_class}'>
			<td align='left'>{$row_color}</td>
			<td align='left'>{$products['productName']}</td>
			<td align='left'>{$products['productType']}</td>
			<td align='left'>{$products['productCode']}</td>
			<td align='center'>{$products['productWarranty']}</td>
			<td align='center'>{$products['productQuantity']}</td>
			<td align='right'>{$unitPrice}</td>
			<td align='right'>{$subTotal}</td>
		</tr>
		";
		$row_color++;
	}
	echo "
			<tr><td><br></td></tr>
			<tr>
				<td colspan='7' align='right'>Installation / Service Charge</td>
				<td align='right'>0.00</td>
			</tr>
			<tr>
				<td colspan='7' align='right'>Value Added Tax</td>
				<td align='right'>0.00</td>
			</tr>
			<tr>
				<td align='left'>Total Paid</td>
				<td align='right' class='total'>{$totalPaid}</td>
				<td colspan='5' align='right'>Discount</td>
				<td align='right'>0.00</td>
			</tr>
			<tr>
				<td colspan='8'><br></td>
			</tr>
	";
	echo "
		<tr>
			<td align='left'> Total Due</td>
			<td class='total' align='right'>{$due}</td>
			<td colspan='5' align='right'>Grand Total</td>
			<td align='right' class='total'>{$grandTotal}</td>
		</tr>
		<tr><td><br></td></tr>
		";
	echo "
		</table>
		</fieldset>
	";
	echo "
		<div id='sign'>
			<div class='cust_sign'>
				<hr>
				Customer's Signature
			</div>
			<div class='auth_sign'>
				<hr>
				Authorized Signature
			</div>
		</div>
		<br><br>
		<div class='nb'>
			<p>Notes: </p>
			<p>#1 : Warranty Will be void if sticker removed, Physically damaged and burn case. All kinds of valid warranty is measured in Months.</p>
			<p>#2 : No Warranty for - Keyboard, Mouse, Power Supply, Speaker, Headphone, Cartridge-Toner, Casing, Webcam, Digital Camera, Card Reader, Bluetooth, Hub, Remote Control, Adapter etc.</p>
			<p>#3 : In any case of sales return/servicing or other problem, you <b>MUST</b> bring the copy of Invoice/bill/payment receipt.</p>
			<p>#4 : VAT (Value Added Tax) is not included.</p>
		</div>
	";
	echo "
		<div class='extended_msg'>
			<a href='javascript:window.print()' class='link_btn'>Print</a>
			<a href='home.php' class='link_btn'>Go Back</a>
		</div>
	";
	echo"
		</div>
	";	
}

/*------------------------------------------------------------------
//This function will make a bill in due
------------------------------------------------------------------*/
function makeDue_bill($bill_id,$flag){
	//unset the sessions if the bill is directly dued
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
	
	//get the customer details from the customers table
	$customers = get_customer_details($bill_id);
	$grandTotal = number_format($customers['grandTotal'],2);
	$message = messages($flag);
	echo "
		<div class='extended_msg'>{$message}</div>
		<div id='print'>
			<fieldset class='fldst'>
				<legend>Customer Details</legend>
				<table align='center' width='98%'  cellspacing='5' cellpadding='5' border='0'>
					<tr>
						<td>Bill ID</td>
						<td>Date</td>
						<td>Time</td>
						<td>Prepared By</td>
					</tr>
					<tr>
						<td>{$bill_id}</td>
						<td>{$customers['billDate']}</td>
						<td>{$customers['billTime']}</td>
						<td>{$customers['preparedBy']}</td>
					</tr>
					<tr><td colspan='4'><br></td></tr>
					<tr>
						<td>Customer Name</td>
						<td>Customer Address</td>
						<td>Customer Cell No</td>
						<td>Customer E-mail</td>
					</tr>
					<tr>
						<td>{$customers['customerName']}</td>
						<td>{$customers['customerAddress']}</td>
						<td>{$customers['customerCell']}</td>
						<td>{$customers['customerEmail']}</td>
					</tr>
					<tr><td colspan='4'><br></td></tr>
				</table>
			</fieldset>
	";
	echo "
		<fieldset class='fldst'>
			<legend>Payment Details</legend>
			<form id='dueform' method='POST' action='create_process.php' name='make_due'>
				<table align='left' width='100%'  cellspacing='5' cellpadding='5' border='0'>
					<tr>
						<td width='20%'>Grand Total</td>
						<td>Paid</td>
					</tr>
					<tr>
						<td class='total' width='20%'>{$grandTotal}</td>
						<td><input type='number' name='payment' size='10' autofocus='autofocus' required='required' autocomplete='on'/></td>
					</tr>
					<tr><td colspan='2'><br></td></tr>
				</table>
				<input type='hidden' name='bill_id' value='{$bill_id}'/>
				<input type='hidden' name='grandTotal' value='{$customers['grandTotal']}'/>
				<input type='submit' name='submitDue' class='submitcart' value='Make Due Invoice'/>
			</form>
		</fieldset>
		<script>
			$('#dueform').validator({ 
			position: 'top left', 
			offset: [-12, 0],
			message: '<div><em/></div>' 
			});
		</script>
	";
	echo"
		</div>
	";	
}

/*---------------------------------------------------------------------
//This function will view all the sales record in DESC order
-----------------------------------------------------------------------*/
function view_allSales($accessCode,$type,$flag){
	$result_set = get_allCustomer_details($type);
	$total_bills = mysql_num_rows($result_set);
	$message = messages($flag);
	if($_SESSION['LOGGEDIN'] == TRUE){
		$row_color = 1;
		echo "
		<div class='extended_msg'>{$message}</div>
		<div class='extended_msg'>Sales Inventory details and settings for {$type}.</div>
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
					<td align='right'>Total Due</td>";
					if($type == "all_sales"){
						echo"	
							<td>Due</td>
						";
					}
					elseif($type == "due_sales"){
						echo"
							<td>Payment</td>
						";
					}
				echo"
				<td>View</td>
				";
				if($accessCode=="green"){
				echo"
					<td>S/H</td>";
					}
				echo"
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
					<td align='right'>{$rows['bill_id']}</td>
					<td>{$rows['customerName']}</td>
					<td>{$rows['customerCell']}</td>
					<td>{$rows['billDate']}</td>
					<td align='right'>{$grandTotal}</td>
					<td align='right'>{$due}</td>
					";
					if($type == "all_sales"){
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
					}
					elseif($type == "due_sales"){
						echo"
							<td><a href='sales_settings.php?bill=".$rows['bill_id']."&choice=pay_bill' class='link_btn2'>Pay</a></td>
						";
					}
					echo "
						<td align='right'><a href='sales_settings.php?bill=".$rows['bill_id']."&choice=view_bill' class='link_btn1'>View</a></td>
					";
					if($_SESSION['ACCESSCODE'] == "green"){
						echo"
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
	else{
		access_denied();
	}
}

/*------------------------------------------------------------------
//This function will view the deatils and pay the due bill
------------------------------------------------------------------*/
function pay_bill($flag,$bill_id){
	//get the customer details from the customers table
	$customers = get_customer_details($bill_id);
	$grandTotal = number_format($customers['grandTotal'],2);
	$totalPaid = number_format($customers['totalPaid'],2);
	$pre_due = $customers['grandTotal']-$customers['totalPaid'];
	$due = number_format($pre_due,2);
	$message = messages($flag);
	echo "
		<div class='extended_msg'>{$message}</div>
		<div id='print'>
			<fieldset class='fldst'>
				<legend>Customer Details</legend>
				<table align='center' width='98%'  cellspacing='5px' cellpadding='5px' border='0'>
					<tr>
						<td>Bill ID</td>
						<td>Date</td>
						<td>Time</td>
						<td>Prepared By</td>
					</tr>
					<tr>
						<td>{$bill_id}</td>
						<td>{$customers['billDate']}</td>
						<td>{$customers['billTime']}</td>
						<td>{$customers['preparedBy']}</td>
					</tr>
					<tr><td colspan='4'><br></td></tr>
					<tr>
						<td>Customer Name</td>
						<td>Customer Address</td>
						<td>Customer Cell No</td>
						<td>Customer E-mail</td>
					</tr>
					<tr>
						<td>{$customers['customerName']}</td>
						<td>{$customers['customerAddress']}</td>
						<td>{$customers['customerCell']}</td>
						<td>{$customers['customerEmail']}</td>
					</tr>
					<tr><td colspan='4'><br></td></tr>
				</table>
			</fieldset>
	";
	echo"
			<fieldset class='fldst'>
				<legend>Customer Details</legend>
				<form id='payment_form' method='POST' action='create_process.php' name='make_payment'>
				<table align='center' width='98%'  cellspacing='5' cellpadding='5' border='0'>
					<tr>
						<td>Total Payable</td>
						<td>Total Paid</td>
						<td>Total Due</td>
						<td>New Payment</td>
					</tr>
					<tr>
						<td>{$grandTotal}</td>
						<td>{$totalPaid}</td>
						<td>{$due}</td>
						<td>
								<input type='hidden' name='bill_id' value='{$bill_id}'/>
								<input type='hidden' name='totalPaid' value='{$customers['totalPaid']}'/>
								<input type='hidden' name='grandTotal' value='{$customers['grandTotal']}'/>
								<input type='number' name='newPayment' size='10' autofocus='autofocus' required='required' autocomplete='on'/>
						</td>
					</tr>
					<tr><td colspan='4'><br></td></tr>
				</table>
				<input type='submit' name='submitPayment' class='submitcart' value='Make New Payment'/>
				</form>
			</fieldset>
		<script>
			$('#payment_form').validator({ 
			position: 'top left', 
			offset: [-12, 0],
			message: '<div><em/></div>' 
			});
		</script>
	";
	echo"
		</div>
	";	
}

/*--------------------------------------------------------------------
//This function will Show/Hide the bill from normal User
-------------------------------------------------------------------*/
function sh_bill($bill_id){
	$query = "SELECT * FROM customers WHERE bill_id = '{$bill_id}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$customers = mysql_fetch_assoc($result);
	$status = $customers['status'];
	$dueStatus = $customers['dueStatus'];
	update_bill_status($bill_id,$status,$dueStatus);
}

/*--------------------------------------------------
//This function will update the bill status 
---------------------------------------------------*/
function update_bill_status($bill_id,$status,$dueStatus){
	if($dueStatus == 0){
		if($status==1){$status_query = "UPDATE customers SET status = '0' WHERE bill_id='{$bill_id}' ";}
		if($status==0){$status_query = "UPDATE customers SET status = '1' WHERE bill_id='{$bill_id}' ";}
		$status_result = mysql_query($status_query);
		if($status_result)
		{
			header("Location:all_sales.php?flag=epsilon");
			exit;
		}
		else
		{
			header("Location:all_sales.php?flag=delta");
			exit;
		}
	}
	else{
		header("Location:all_sales.php?flag=phi");
		exit;
	}
}

/*-------------------------------------------------------------
//This Function will Show/Hide Payment Slips
-------------------------------------------------------------*/
function sh_payment($payment_id){
	$query = "SELECT * FROM payments WHERE payment_id = '{$payment_id}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$payments = mysql_fetch_assoc($result);
	$status = $payments['status'];
	update_payment_status($payment_id,$status);
}

/*--------------------------------------------------
//This function will update the payment status 
---------------------------------------------------*/
function update_payment_status($payment_id,$status){
	if($status==1){$status_query = "UPDATE payments SET status = '0' WHERE payment_id='{$payment_id}' ";}
	if($status==0){$status_query = "UPDATE payments SET status = '1' WHERE payment_id='{$payment_id}' ";}
	$status_result = mysql_query($status_query);
	if($status_result)
	{
		header("Location:search_payments.php?flag=epsilon");
		exit;
	}
	else
	{
		header("Location:search_payments.php?flag=delta");
		exit;
	}
}
/*----------------------------------------------------------------
//This function will view the payment receipt
----------------------------------------------------------------*/
function payment_receipt($bill_id,$payment_id){
	$customers = get_customer_details($bill_id);
	$grandTotal = number_format($customers['grandTotal'],2);
	$due = number_format(($customers['grandTotal']-$customers['totalPaid']),2);
	echo"
		<div id = 'title'>
			<h1>Power IT Computer</h1>
			<h4>Show Room: Shop No: 226, Alaka Nodi Bangla Shopping Center (1st Floor), Mymensingh</h4>
			<h4>Head Office: 51, Jail Road, Mymensingh</h4>
			<h4>Cell No: 01716 - 36 42 00 &nbsp; OR &nbsp; 01675 - 30 11 61</h4>
			<h4>E-mail: info@powertitbd.com</h4>
			<hr>
		</div>
	";
	echo "
	<div id='print'>
		<fieldset class='fldst'>
			<legend>Customer Details</legend>
			<table align='center' width='98%'  cellspacing='5' cellpadding='5' border='0'>
				<tr>
					<td>Bill ID</td>
					<td>Sales Date</td>
					<td>Sales Time</td>
					<td>Prepared By</td>
				</tr>
				<tr>
					<td>{$bill_id}</td>
					<td>{$customers['billDate']}</td>
					<td>{$customers['billTime']}</td>
					<td>{$customers['preparedBy']}</td>
				</tr>
				<tr><td colspan='4'><br></td></tr>
				<tr>
					<td>Customer Name</td>
					<td>Customer Address</td>
					<td>Customer Cell No</td>
					<td>Customer E-mail</td>
				</tr>
				<tr>
					<td>{$customers['customerName']}</td>
					<td>{$customers['customerAddress']}</td>
					<td>{$customers['customerCell']}</td>
					<td>{$customers['customerEmail']}</td>
				</tr>
				<tr><td colspan='4'><br></td></tr>
			</table>
		</fieldset>
		";
		$payments = get_payment_details($payment_id);
		$paidAmount = number_format($payments['paidAmount'],2);
		$totalPaid = number_format($payments['totalPaid'],2);
		$totalDue = number_format($payments['totalDue'],2);
		echo"
					<fieldset class='fldst'>
						<legend>Payment Details</legend>
						<table align='center' width='98%'  cellspacing='5' cellpadding='5' border='0'>
							<tr>
								<td>Payment ID</td>
								<td>Payment Date</td>
								<td>Total Payable</td>
								<td>Total Paid</td>
								<td>Total Due</td>
								<td>New Payment</td>
							</tr>
							<tr>
								<td>{$payments['payment_id']}</td>
								<td>{$payments['paymentDate']}</td>
								<td class='total'>{$grandTotal}</td>
								<td>{$totalPaid}</td>
								<td>{$totalDue}</td>
								<td class='total'>{$paidAmount}</td>
							</tr>
							<tr><td colspan='4'><br></td></tr>
						</table>
					</fieldset>
			";
	echo "
		<div id='sign'>
			<div class='cust_sign'>
				<hr>
				Customer's Signature
			</div>
			<div class='auth_sign'>
				<hr>
				Authorized Signature
			</div>
		</div>
		<div class='nb'>
			<br>
		</div>
	";
	echo "
		<div class='extended_msg'>
			<a href='javascript:window.print()' class='link_btn'>Print</a>
			<a href='home.php' class='link_btn'>Go Back</a>
		</div>
	";
	echo"
		</div>
	";
}

/*----------------------------------------------------------------------
//This function will search payment by bill id or Payement ID
-----------------------------------------------------------------------*/
function searchPayments_by_id($flag){
	if($_SESSION['LOGGEDIN']==TRUE){
		$message = messages($flag);
		echo "
			<div class='extended_msg'>{$message}</div>
			<form id='displayBack' method='POST' action='search_payments.php'>
				<p>Please select a type and write the Bill ID or Payment ID to show the Payments</p>
				<div class='displayAlpha'>";
		echo "
				<select name='searchType'>
					<option value='none'>---- Select ----</option>
					<option value='bill_id'>Bill ID</option>
					<option value='payment_id'>Payment ID</option>
				</select>";
		echo "
				<input type='text' name='searchInput' size='20'/>
				";
		echo "
				</div>
				<div class='clear'></div>
				<button type='submit' name='searchPayments_byid'>Search Payment</button>
			</form>
		";
	}
	else{
		access_denied();
	}
}
/*------------------------------------------------------------------
//This function will get all the payment details by bill ID
------------------------------------------------------------------*/
function get_payment_details($payment_id){
	$query = "SELECT * FROM payments WHERE payment_id = '{$payment_id}' ";
	$result_set = mysql_query($query);
	confirm_query($result_set);
	$payment_set = mysql_fetch_assoc($result_set);
	return $payment_set;
}

/*------------------------------------------------------------------
//This function will get all the customer details by bill ID
------------------------------------------------------------------*/
function get_customer_details($bill_id){
	$query = "SELECT * FROM customers WHERE bill_id = '{$bill_id}' ";
	$result_set = mysql_query($query);
	confirm_query($result_set);
	$customer_set = mysql_fetch_assoc($result_set);
	return $customer_set;
}

/*------------------------------------------------------------------
//This function will get all the customer details by bill ID
------------------------------------------------------------------*/
function get_allCustomer_details($type){
	if($type == "all_sales"){
		$query = "SELECT * FROM customers ORDER BY bill_id DESC";
	}
	elseif($type == "due_sales"){
		$query = "SELECT * FROM customers WHERE dueStatus = '1' ORDER BY bill_id DESC";
	}
	$result_set = mysql_query($query);
	confirm_query($result_set);
	return $result_set;
}

/*------------------------------------------------------------------
//This function will get all the sold products by Bill ID 
------------------------------------------------------------------*/
function get_sales_product_details($bill_id){
	$query = "SELECT * FROM bill_details WHERE bill_id = '{$bill_id}' ";
	$result_set = mysql_query($query);
	confirm_query($result_set);
	return $result_set;
}

/*--------------------------------------------------------
//This function will e-mail a new password
--------------------------------------------------------*/
function send_password($email){
	$password = get_password();
	$user = get_userName($email);
	
	$to = $email;
	$subject = 'New Password for Login : Power IT Computers Ltd.';
	$headers = 'From: admin@poweritbd.com' . "\r\n" .
					 'Reply-To: admin@poweritbd.com' . "\r\n" .
					 'X-Mailer: PHP/' . phpversion();
	$message = "Dear" . $user['userName'] ."\n";
	$message .= "Your password has been reset and here are your login credentials: \n\n";
	$message .= "User Name: " . $user['userName'] . "\n";
	$message .= "Password: " . $password . "\n\n";
	$message .= "N.B: IF YOU HAVE ANY PROBLEM OR WANT TO CHANGE THE PASSWORD THEN PLEASE CONTACT WITH THE ADMIN.\n";
	$message .= "admin@poweritbd.com\n";
	$message .= "Thank You!";
	
	$send_mail = mail($to,$subject,$message,$headers);
	
	if($send_mail){
		return TRUE;
	}
	else{
		return FALSE;
	}
}

/*-----------------------------------------------------
//This function will get the random password
----------------------------------------------------*/
function get_password(){
	$length=6; //string length
	$uselower=1; //use lowercase letters
	$useupper=1; // use uppercase letters
	$usespecial=0; //use special characters
	$usenumbers=1; //use numbers
	$prefix='';

	$key = $prefix;
	// Seed random number generator
		srand((double)microtime() * rand(1000000, 9999999));
		$charset = "";
		if ($uselower == 1) $charset .= "abcdefghijkmnopqrstuvwxyz";
		if ($useupper == 1) $charset .= "ABCDEFGHIJKLMNPQRSTUVWXYZ";
		if ($usenumbers == 1) $charset .= "0123456789";
		if ($usespecial == 1) $charset .= "~#$%^*()_+-={}|][";
		while ($length > 0) {
			$key .= $charset[rand(0, strlen($charset)-1)];
			$length--;
		}
		return $key;
}

/*-------------------------------------------------------------
/This function will get the userName by it's email
-------------------------------------------------------------*/
function get_userName($email){
	$query = "SELECT * FROM users WHERE eMail = '{$email}' ";
	$result = mysql_query($query);
	confirm_query($result);
	$user_set = mysql_fetch_assoc($result);
	return $user_set;
}

?>