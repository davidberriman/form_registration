<?php

session_start();
require_once "../include/membersite_config.php";
require_once '../include/csrf_token_functions.php'; // token is valid
require_once "../include/reset_token_functions.php"; // all the reset code
require_once("../include/class.login.php");

// initialize variables to default values
$username = "";
$message = "";
$linkSent = false;
$divClass = "errorBox";


if(csrf_token_is_valid() && csrf_token_is_recent() && isset($_POST['submitted'])) {
	
	// retrieve the values submitted via the form
	$email = $_POST['email'];
	
	if(checkRegistration($username)){
			$message = "You have not completed your registration! An email was sent to your account. You need to click on the link in that email to complete the registration";
		
	}else{

		if(has_presence($email)) {
						
			$user = new Login();
			if($user->get_user_for_field_with_value("email", $email))
		    {
				
				// create a toen in the resetpassword column
				if(create_reset_token($email))
				{
					// email token to the user
					if(email_reset_token($email))
					{
						// Message returned is the same whether the user 
						// was found or not, so that we don't reveal which 
						// usernames exist and which do not.
						$divClass = "warnBox";
						$message = "A link to reset your password has been sent to the email address on file.";
						$linkSent = true;
					}else
					{
						$message = "There was an error sending a password reset email. Please contact support";
					}
				}
				
			}else
			{
				// tell user that pasword eas sent even if account doesn't exists so they 
				// can't gather info on account holders
				$message = "A link to reset your password has been sent to the email address on file.";
				$linkSent = true;
			}
		
		} else {
			$message = "Please enter your email address.";
		}
	
	}
}else{
	unset($message);	
}
	
?>