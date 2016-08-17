<?php

session_start();
require_once "../include/membersite_config.php";
require_once '../include/csrf_token_functions.php'; // token is valid
require_once "../include/reset_token_functions.php"; // all the reset code
require_once("../include/class.login.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

if(isset($_POST['submitted']))
{
		
    if(!csrf_token_is_valid() || !csrf_token_is_recent()) {
	  $message = "Sorry, request was not valid.";
	  return ;
    } 
  
	// retrieve the values submitted via the form
	$oldPassword = $_POST['oldpwd'];
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
		
	else if($password !== $password_confirm) {
		$message = "Password confirmation does not match password.";
		return;
	}
	
		
	if($fgmembersite->ChangePassword($oldPassword, $password))
	{
		$fgmembersite->RedirectToURL("changed-pwd.html");
	}else
	{
		$message = $fgmembersite->GetErrorMessage();
	}
	
  	
}else{
	unset($message);	
}

?>