<?php
// Reset token functions
require_once 'functions.php'; // token is valid
require_once("class.login.php");
require_once("class.password.php");
require_once("sanitize.php");

//-------------------------------------------------
// This function generates a string that can be
// used as a reset token.
//-------------------------------------------------
function reset_token() {
	return md5(uniqid(rand()));
}


//-------------------------------------------------
// Looks up a user and sets their reset_token to
// the given value. Can be used both to create and
// to delete the token.
//-------------------------------------------------
function set_user_reset_token($email, $token_value) {
	
	$user = new Login();
	if($user->get_user_for_field_with_value("email", $email))
    {
		if(updateResetPassword($email, $token_value))
		{
			return true;
		}
	}
	
	return false;
}


//-------------------------------------------------
// Add a new reset token to the user
//-------------------------------------------------
function create_reset_token($emailaddress) {
	$token = reset_token();
	return set_user_reset_token($emailaddress, $token);
}


//-------------------------------------------------
// Remove any reset token for this user.
//-------------------------------------------------
function delete_reset_token($emailaddress) {
	$token = null;
	return set_user_reset_token($emailaddress, $token);
}


//-------------------------------------------------
// Returns the user record for a given reset token.
// If token is not found, returns null.
//-------------------------------------------------
function find_user_with_token($token) {
	if(!has_presence($token)) {
		// We were expecting a token and didn't get one.
		return null;
	} else {
		$user = findResetToken($token);
		// Note: find_one_in_fake_db returns null if not found.
		return $user;
	}
}


//-------------------------------------------------
// A function to email the reset token to the email
// address on file for this user.
// This is a placeholder since we don't have email
// abilities set up in the demo version.
//-------------------------------------------------
function email_reset_token($username) {
		
	$user = new Login();
	if($user->get_user_for_field_with_value("email", $username))
    {

		// This is where you would connect to your emailer
		// and send an email with a URL that includes the token.
		
		$data = getResetDetails($username);
				
        $email = $data['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$data['name']);
        
        $mailer->Subject = "Your reset password request";

        $mailer->From = "passwordReset.com";
		
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        
        $link = $scriptFolder.
                '/resetpwd.php?email='.
                urlencode($email).'&code='.
                urlencode($data['token']);

        $mailer->Body ="Hello ".$data['name']."\r\n\r\n".
        "There was a request to reset your password ".
        "Please click the link below to complete the request: \r\n".$link."\r\n".
        "Regards,\r\n";
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
		
		

	} else {
		return false;
	}
	
}


//----------------------------------------------------
// look for user in the database using the email field
//----------------------------------------------------
function checkRegistration($email){
	
	$user = new Login();
	if($user->get_user_for_field_with_value("email", $email))
    {
		$confirmCode = $user->confirmcode;
		
		if($confirmCode != "y")
		{
			return true;
		}
		
		return false;
	}
	
	return false;
							  	
}



//----------------------------------------------------
// look for user in the database using the reset token
//----------------------------------------------------
function findResetToken($token){
	
	$user = new Login();
	if($user->get_user_for_field_with_value("resetpassword", $token))
    {
		$email = $user->email;
		
		return $email;
	}
	
	return false;
							 	
}



//------------------------------------------------------
// look for user in the database using the email & token
//------------------------------------------------------
function findResetTokenAndEmail($token, $email){
	
	$user = new Login();
	if($user->get_user_for_field_and_field_with_value("resetpassword", $token, "email", $email))
    {
		$email = $user->email;
		
		return $email;
	}
	
	return false;
	
}



//----------------------------------------------------
// update the reset password field
//----------------------------------------------------
function updateResetPassword($email, $token){
	
	$user = new Login();
	if($user->get_user_for_field_with_value("email", $email))
    {
		$user->resetpassword = $token;
		
		if($user->update())
		{
			return true;
		}
	}
	
	return false;			
}



//----------------------------------------------------
// get reset details for the reset email
//----------------------------------------------------
function getResetDetails($email){
	
	$user = new Login();
	if($user->get_user_for_field_with_value("email", $email))
    {
		$emailAddress  = $user->email;
		$resetpassword = $user->resetpassword;
		$name = $user->forename ." ". $user->surname;
		
		$thisRec['email'] = $emailAddress;
		$thisRec['token'] = $resetpassword;
		$thisRec['name'] = $name;
		return $thisRec;
	}
	
	return false;	
}



//----------------------------------------------------
// Change the password on the users account. This uses
// the Password class so all encryption is consistent
//----------------------------------------------------
function ChangePass($email, $newpwd)
{	
	$passwordUpdater = new Password();
	
	if($passwordUpdater->updatePasswordForEmail($newpwd, $email)){
		return true;
	}
	
	return false;	
}

?>
