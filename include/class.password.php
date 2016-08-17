<?php
# ========================================================================#
#
#  Class for verifying and setting the password so it is always the same
#  with the same encryption etc.
#
# ========================================================================#

require_once("database.php");
require_once("sanitize.php");
require_once("class.error_log.php");

class Password extends MySQLDatabase
{
	protected static $table_name="login";
	
	public $email;
	public $password;
	
	public $errors = array();
		

	// $user in second arg is an instance of the Login class
	public function isPasswordValidForUser($password, $user){
		
		$storedpassword = $user->password;
		
		// sanitize password before check as user may
		// have entered characters that were converted		
		$sanitize = new Sanitize();
		$password = $sanitize->clean($password);
		
		if (password_verify($password, $storedpassword)) {
			
			return true;
		}
		
		return false;
	}
	
	
	
		
	public function isPasswordValidForEmail($password, $email){

		$user = new Login();
		
        if($user->get_user_for_field_with_value("email", $email))
        {
			$storedpassword = $user->password;
		
			// sanitize password before check as user may
			// have entered characters that were converted			
			$sanitize = new Sanitize();
			$password = $sanitize->clean($password);
		
			if (password_verify($password, $storedpassword)) {
			
				return true;
			}
        }
		
		return false;
	}
	
	
	// $user in second arg is an instance of the Login class
	public function updatePasswordForUser($password, $user){
						
		if(empty($password) || !isSet($password) || $password == "")
		{
			return false;
		}
		
		// sanitize password before check as user may
		// have entered characters that were converted	
		$sanitize = new Sanitize();
		$password = $sanitize->clean($password);
						
		$user->password = password_hash($password, PASSWORD_DEFAULT);
		
	    if(!$user->update())
	    {
	        return false;
	    }
		
		return true;
	}
	
	
	public function updatePasswordForEmail($password, $email){
		
		if(empty($password) || !isSet($password) || $password == "")
		{
			return false;
		}
		
		$user = new Login();
						
		if(!$user->get_user_for_field_with_value("email", $email))
	    {
	        return false;
	    }
		
		// clean it first incase they have chars in the password
		// which will be encoded		
		$sanitize = new Sanitize();
		$password = $sanitize->clean($password);
				
		$user->password = password_hash($password, PASSWORD_DEFAULT);
		
	    if(!$user->update())
	    {
	        return false;
	    }
		
		return true;
	}

		
}

?>