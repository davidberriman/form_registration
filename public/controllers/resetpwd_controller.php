<?php

session_start();
require_once "../include/membersite_config.php";
require_once '../include/csrf_token_functions.php'; // token is valid
require_once "../include/reset_token_functions.php"; // all the reset code
require_once("../include/class.login.php");

$message = "";
$token = $_GET['code'];

// Confirm that the token sent is valid
$user = findResetToken($token);
if(!$user) {
	// Token wasn't sent or didn't match a user.
	$fgmembersite->RedirectToURL("reset-pwd-req.php");
}

$divClass = "errorBox";
if(csrf_token_is_valid() && csrf_token_is_recent() && isset($_POST['email'])) {

    // CSRF tests passed--form was created by us recently.
	$email =  strtolower($_POST['email']);
	
	$userConfirmed = new Login();
	if($userConfirmed->get_user_for_field_and_field_with_value("resetpassword", $token, "email", $email))
    {
		// retrieve the values submitted via the form
	    $password = $_POST['password'];
	    $password_confirm = $_POST['passwordConfirm'];
	    
		if(!has_presence($password) || !has_presence($password_confirm)) {
			$message = "Password and Confirm Password are required fields.";
			return;
		}
		
		// ----------------------------------------------------
		// uncomment this to add password strength validation
		// ----------------------------------------------------
		/* 
		$passwordLength = 8;
		else if(!has_length($password, $passwordLength)) {
			$message = "Password must be at least 8 characters long.";
			return;
		} else if(!has_format_matching($password, '/[^A-Za-z0-9]/')) {
			$message = "Password must contain at least one character which is not a letter or a number.";
			return;
		}
		*/
		
		if($password !== $password_confirm) {
			$message = "Password confirmation does not match password.";
			return;
		} 
		
		// password and password_confirm are valid
		// Hash the password and save it to the fake database
		if(ChangePass($email,$password))
		{
			delete_reset_token($email);
			//redirect_to('login.php');
			$fgmembersite->RedirectToURL("changed-pwd.html");
		}else
		{
			$message = "There was an error changing your password. Please contact support.";	
		}
		
		
	}else
	{
		$message = "Your details were not recognised. Please contact support.";	
	}


}else{
	$divClass = "infoBox";
	$message = "Please confirm your email address";	
}
	
?>