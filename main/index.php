<?php ob_start(); ?>

<?php
	//start the session
	session_start();
	if (!isset($_SESSION['SESSION'])) require ("../includes/session_init.php");
	
	//include necessary files and functions
	require_once("../includes/db_connection.php");
	require_once("../includes/functions.php");
	
	//check the browser doen't cache the page
	header ("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	
	//set the time zone
	@date_default_timezone_set('Asia/Dacca');
		
	//if the user is logged in then send back to home page
	if($_SESSION['LOGGEDIN'] == true)
	{
		header("Location:home.php");
		exit;
	}
?>

<?php index_header(); ?>

	<script language="javascript">
		$(document).ready(function()
		{
			$("#login_form").submit(function()
			{
				//remove all the class add the messagebox classes and start fading
				$("#msgbox").removeClass().addClass('messagebox').text('Validating your login credentials....').fadeIn(1000);
				//check the username exists or not from ajax
				$.post("login_process.php",{ user_name:$('#username').val(),password:$('#password').val(),rand:Math.random() } ,function(data)
				{
				  if(data=='yes') //if correct login detail
				  {
					$("#msgbox").fadeTo(200,0.1,function()  //start fading the messagebox
					{ 
					  //add message and change the class of the box and start fading
					  $(this).html('Logging in.....').addClass('messageboxok').fadeTo(900,1,
					  function()
					  { 
						 //redirect to secure page
						 document.location='home.php';
					  });
					  
					});
				  }
				  else 
				  {
					$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
					{ 
					  //add message and change the class of the box and start fading
					  $(this).html('Invalid or wrong login credentials ! Please try again...').addClass('messageboxerror').fadeTo(900,1);
					});		
				  }
						
				});
				return false; //not to post the  form physically
			});
			//now call the ajax also focus move from 
			$("#password").blur(function()
			{
				$("#login_form").trigger('submit');
			});
		});
	</script>

<script language="javascript">
$(document).ready(function()
{
	$("#reset").submit(function()
	{
		//remove all the class add the messagebox classes and start fading
		$("#msgbox2").removeClass().addClass('messagebox').text('Validating your E-Mail....').fadeIn(1000);
		//check the username exists or not from ajax
		$.post("passwordreset_process.php",{ email:$('#password_reset').val(),rand:Math.random() } ,function(data)
		{
		  if(data=='yes') //if correct login detail
		  {
			$("#msgbox2").fadeTo(200,0.1,function()  //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Sending Your New Password.....').addClass('messageboxok').fadeTo(900,1,
			  function()
			  { 
				 //redirect to secure page
				 document.location='index.php';
			  });
			  
			});
		  }
		  else 
		  {
			$("#msgbox2").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Invalid or wrong E-mail ! Please try again...').addClass('messageboxerror').fadeTo(900,1);
			});		
		  }
				
		});
		return false; //not to post the  form physically
	});
	//now call the ajax also focus move from 
	$("#password_reset").blur(function()
	{
		$("#reset").trigger('submit');
	});
});
</script>

<?php index_body_top(); ?>

				<div id="index_container">
					<div id="left_content">
						<h3>Authenticate Yourself !</h3>
						<form name="login" method="POST" action="" id="login_form">
							<p class="first_field">Username<input name="username" type="text" class="field" maxlength="16" id="username"  required="required" autocomplete="on"/></p>
							<p class="second_field">Password<input name="password" type="password" class="field" maxlength="16" id="password"  required="required" autocomplete="on"/></p>
							<p class="submit_login"><input name="" type="image" src="../images/Login_button.jpg" /><span id="msgbox" style="display:none"></span></p>
						</form>
					</div>
					<div id="right_content">
						<h3>Forgot your username or password ?</h3>
						<p class="right_text">Don't worry! Just write down the e-mail address that you have used in your profile and submit it. We will send you the username with a new password. You can change your password after logging in with the new password. Thank You!</p>
						<form name="password_reset" method="POST" action="" id="reset">
							<p>E-mail Address<input name="password_reset" type="email" id="password_reset" required="required" autocomplete="on"/></p>
							<p class="res_pass"><input name="reset" type="image" src="../images/resrt_password.jpg" /><span id="msgbox2" style="display:none"></span></</p>
						</form>
					</div>
				</div>
				
<?php footer(); ?>

<?php ob_flush(); ?>