<?php
	ob_start();
	session_start();
	
	require_once("class.user.php");
	$session = new USER();
	
	# if user is not logged, redirect to login page.
	
	if(!$session->is_loggedin())
	{
		// session no set redirects to login page
		$session->redirect(WEB_ROOT);
	}