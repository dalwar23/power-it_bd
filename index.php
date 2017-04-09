<?php
	/*Starts the output buffer*/
	ob_start();
	
	/*Redirect to the main site*/
	header("Location:main/");
	exit();
	
	//flushes the output buffer
	ob_flush();
?>