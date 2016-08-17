<?php

session_start();
require_once "../include/membersite_config.php";
require_once '../include/csrf_token_functions.php'; // token is valid
require_once("../include/class.login.php");
require_once("../include/sanitize.php");
require_once("../include/class.password.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}



// save failed login to database so we can throttle user
// if they make too many attempts
function varificationFailed($email)
{
	global $message, $fgmembersite;
		
	$message = "WARNING <br/> Your account was not recognised.";
	
	// record failed login in the failed_logins table
	// so we can throttle the user if they carry on doing so
	$fgmembersite->record_failed_login($email);
	
	// see how many failed logins they have had so we can warn them
	// if they are going to be throttled;
	$failed_login = $fgmembersite->checkForFailedLogin($email);
	
	// warn user if they have tried to delete someone elses account too many times
	if($failed_login['count'] == 14)
	{
		$message = "WARNING <br/><br/> Please do not try and delete another users account. <br/><br/>If you try again you will be removed from the website and will not be able to log in for 30 minutes.";
	}
	
	if($failed_login['count'] > 14)
	{
		$fgmembersite->RedirectToURL("logout.php");
	}
}



function varificationSuccess($email)
{
	global $fgmembersite;
	
	$fgmembersite->clear_failed_logins($email); 
}



if(isset($_POST['submitted']))
{

	// check the form submission is valid
  if(!csrf_token_is_valid() || !csrf_token_is_recent()) {
	$message = "Sorry, request was not valid.";
  } else {
  
		// retrieve the values submitted via the form
		$email = $_POST['username'];
		$password = $_POST['password'];
	
		// check the user has entered email / password
		if(!isset($password) || $password == "" || !isset($email) || $email == "") {
			$message = "Email and password are required fields.";
		}
						
		// get eamil address user is logged in with
		$loggedInWithEmail = $fgmembersite->UserEmail();
		
		// check that the email entered is what the user is logged in with
		// so they are not trying to delete someone elses account.
		if($email != $loggedInWithEmail)
		{
			varificationFailed($email);
			return;
		}

		// check user details are correct
		$user = new Login();
		if($user->get_user_for_field_with_value("email", $email))
	    {
						
			$passwordVerify = new Password();
			if($passwordVerify->isPasswordValidForUser($password, $user)){
				
				varificationSuccess($email);
							 
				// if we get this far then user is who theysay they are
				// and they want to delete their account
			   if($user->delete())
			   {
				   $fgmembersite->LogOut();
				   $fgmembersite->RedirectToURL("account-deleted.php");
			   }else
			   {
				   $message = "ERROR - There was an error deleting your account. Please contact support.";
			   }
			}else
			{
				varificationFailed($email);
				return;
			}
			
		}else
		{
			varificationFailed($email);
			return;
		}

	}
  	
}else{
	unset($message);	
}

?>