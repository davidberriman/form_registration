<?PHP
/*
    Registration/Login script from HTML Form Guide
    V1.0

    This program is free software published under the
    terms of the GNU Lesser General Public License.
    http://www.gnu.org/copyleft/lesser.html
    

This program is distributed in the hope that it will
be useful - WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.

For updates, please visit:
http://www.html-form-guide.com/php-form/php-registration-form.html
http://www.html-form-guide.com/php-form/php-login-form.html


*******************************************************
******* The above is the original decleration *********
*******************************************************

The text about this code having no warranty still applies!

Additions have been made by David Berriman to improve the security and architecture

*/
require_once("class.phpmailer.php");
require_once("class.login.php");
require_once("class.password.php");
require_once("class.login_fail.php");
require_once("class.error_log.php");
require_once("formvalidator.php");
require_once("sanitize.php");

class FGMembersite
{
    var $admin_email;
	var $admin_name;
    var $from_address;
    
	var $db_host;
    var $username;
    var $pwd;
    var $database;
    var $connection;
    var $rand_key;
    
    var $error_message;
	var $error;
    
    //-----Initialization -------
    function InitDB($host,$uname,$pwd,$database)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        
    }
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    
    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }
	
    function SetAdminName($name)
    {
        $this->admin_name = $name;
    }
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
    
    //-------Main Operations ----------------------
	
	
	function clean($data) 
	{		
		$sanitize = new Sanitize();
		$data = $sanitize->clean($data);
		return $data;
	}
	
	
	
	function outputArray($arr){
	
		$val = "";
	
		foreach ($arr as $key => $value) {
			if(isset($val)){
				$val= $val."Key: $key; Value: $value<br />\n";
			}else{
				$val = "Key: $key; Value: $value<br />\n";
			}
		}	
		return $val;
	}
	
	
	function createErrorLog($error)
	{
		// save error message to file / database
		$log = new ErrorLog();
		$log->createErrorLog($error);
	}

	
    function RegisterUser()
    {
        if(!isset($_POST['submitted']))
        {
           return false;
        }
		
		// check that the captcha was correct
		if($_POST['captcha'] != $_SESSION['digit']){
			$this->HandleError("Sorry, the Image letters were incorrect!");
            return false;
		}
        
        $formvars = array();
        
        if(!$this->ValidateRegistrationSubmission())
        {
            return false;
        }
        
        $this->CollectRegistrationSubmission($formvars);
        
        if(!$this->SaveToDatabase($formvars))
        {
            return false;
        }
        
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }

        $this->SendAdminIntimationEmail($formvars);
        
        return true;
    }



    function ConfirmUser($code)
    {
        if(empty($code)||strlen($code)<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
		
        $user_rec = array();
        
		try
		{ 
	        if(!$this->UpdateDBRecForConfirmation($user_rec, $code))
	        {
	            return false;
	        }
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from ConfirmUser fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($user_rec));
			return false;
					
		}
        
        $this->SendUserWelcomeEmail($user_rec);
        
        $this->SendAdminIntimationOnRegComplete($user_rec);
        
        return true;
    }
	
	
	 // ============================ this counts failed logins and throttles user if there are more than 15 ======================== //

	function record_failed_login($username) {
			
		  $failed_login = $this->checkForFailedLogin($username);
  
		  if(!$failed_login) {
			  $this->saveFailedLoginToDatabase($username, 1, time());
		  
		  } else {
			  // existing failed_login record
			  $count = $failed_login['count'];
			  $count = $count+ 1;
			  $time = time();		
			  $this->updateFailedLoginToDatabase($username, $count, $time);
		  }
	  
		  return true;
	  }
  
	  function clear_failed_logins($username) {
		  $failed_login = $this->checkForFailedLogin($username);
		    
		  if(isset($failed_login)) {
			  // set the count back to 0
			  $count = 0;
			  $time = time();
			  $this->updateFailedLoginToDatabase($username, $count, $time);
		  }
	  
		  return true;
	  }
  
	  // Returns the number of minutes to wait until logins 
	  // are allowed again.
	  function throttle_failed_logins($username) {
		  
		  // THROTTLE_VALUE is how many login attempts
		  // the user can have before the login proces
		  // is locked down - prevents brute force attacks
		  $throttle_at = THROTTLE_VALUE;
		  
		  // the number of minutes a user cannot attempt 
		  // login after they have been throttled
		  $delay_in_minutes = THROTTLE_MINUTES;
		  
		  $delay = 60 * $delay_in_minutes;
	  
		  $failed_login = $this->checkForFailedLogin($username);
		  $count = $failed_login['count'];
  
		  // Once failure count is over $throttle_at value, 
		  // user must wait for the $delay period to pass.
		  if($failed_login && $count >= $throttle_at) {
			  $remaining_delay = ($failed_login['time'] + $delay) - time();
			  $remaining_delay_in_minutes = ceil($remaining_delay / 60);
			  return $remaining_delay_in_minutes;
		  } else {
			  return 0;
		  }
	  }
  
	  function saveFailedLoginToDatabase($user, $count, $time){
		  
  		  $loginFails = new LoginFails();
		  
		  $loginFails->username = $user;
		  $loginFails->login_count = $count;
		  $loginFails->last_time = $time;
		
          if($loginFails->create())
          {
			  return true;
		  }
	  }
  
	  function updateFailedLoginToDatabase($user, $count, $time){
		  
  		  $loginFails = new LoginFails();
		  
		  $loginFails->username = $user;
		  $loginFails->login_count = $count;
		  $loginFails->last_time = $time;
		  		
          if($loginFails->update())
          {
			  return true;
		  }
	  }
  
  
	  function checkForFailedLogin($username){
		  		  
  		  $loginFails = new LoginFails();		
          if($loginFails->get_user_for_field_with_value("username", $username))
          {
			  $userCount = $loginFails->login_count;
			  $userTime = $loginFails->last_time;
			  
			  if(!isSet($userCount))
			  {
			  	  $userCount = 0;
			  }
			  
			  $thisCount['count'] = $userCount;
			  $thisCount['time'] = $userTime;
			  $thisCount['username'] = $username;
			  return $thisCount;
		  }
		  
		  return false;
	  }
	// ---------------------------- END - this counts failed logins and throttles user if there are more than 15 -------//  

    
    function Login()
    {
        if(empty($_POST['username']))
        {
            $this->HandleError("UserName is empty!");
            return false;
        }
        
        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
        
        $username = $_POST['username'];
        $password = $_POST['password'];
		
		// apply a delay to prevent brute force attacks
		$throttle_delay = $this->throttle_failed_logins($username);
		if($throttle_delay > 0){
			$message  = "Too many failed logins. ";
			$message .= "You must wait {$throttle_delay} minutes before you can attempt another login.";
			$this->HandleError($message);
			$this->createErrorLog("User ".$_POST['username'].
			"Has been throttled for incorrect username/password <br/>Email address is ".$_POST['username'],E_ALL, $_POST['username']);
			return false;
		}
        
        if(!isset($_SESSION)){ session_start(); }
        
		try
		{ 
	        if(!$this->CheckLoginInDB($username,$password))
	        {
			  	// record the failed login to throttle the user to prevent brute force attacks
			  	$this->record_failed_login($username);
	            return false;
	        }else{
				$this->clear_failed_logins($username);  
			}
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from Login: fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($user_rec));
					
		}
        
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        
        return true;
    }
    
	
	
    function CheckLogin()
    {
         if(!isset($_SESSION)){ session_start(); }

         $sessionvar = $this->GetLoginSessionVar();
         
         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }
    
	
	
    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }
	
	
    
    function UserEmail()
    {
        return isset($_SESSION['email_of_user'])?$_SESSION['email_of_user']:'';
    }
	
	
    
    function LogOut()
    {
        session_start();
        
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;
        
        unset($_SESSION[$sessionvar]);
    }
	
	
    
    function EmailResetPasswordLink()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        $user_rec = array();
        
		
		try
		{ 
	        if(false === $this->GetUserFromEmail($_POST['email'], $user_rec))
	        {
	            return false;
	        }
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from EmailResetPasswordLink fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($user_rec));
					
		}
		
        if(false === $this->SendResetPasswordLink($user_rec))
        {
            return false;
        }
        return true;
    }
    

    
    function ChangePassword($oldPassword, $newPassword)
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }
		        
        if(empty($oldPassword) || !isSet($oldPassword) || $oldPassword == "")
        {
            $this->HandleError("Old password is empty!");
            return false;
        }
        if(empty($newPassword) || !isSet($newPassword) || $newPassword == "")
        {
            $this->HandleError("New password is empty!");
            return false;
        }
		        
        $user_rec = array();
   		try
		{ 
	        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
	        {
	            return false;
	        }
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from ChangePassword fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($user_rec));
					
		}
        		
		try
		{ 
	        if(!$this->CheckLoginInDB($user_rec['email'],$oldPassword))
	        {
				$this->error_message = "";
	            $this->HandleError("The old password was not correct!");
	            return false;
	        }
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from ChangePassword fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($user_rec));
					
		}
		        		
		try
		{ 
	        if(!$this->ChangePasswordInDB($user_rec, $newPassword))
	        {
				$this->error_message = "";
	            $this->HandleError("There was an error updating your password");
	            return false;
	        }
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from ChangePassword fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($user_rec));
					
		}
		
        return true;
    }
    
	
	
    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }    
    
    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
    
    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }
    
    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        //$errormsg = nl2br(htmlentities($this->error_message));
		
		$errormsg = htmlentities($this->error_message);
		
        return $errormsg;
    }  
	  
    //-------Private Helper functions-----------
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysql_error());
    }
    
    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="admin@$host";
        return $from;
    } 
    
    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
	    
	
    function UpdateDBRecForConfirmation(&$user_rec, $code)
    {
		$user = new Login();
		
        if(!$user->get_user_for_field_with_value("confirmcode", $code))
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
		
		$user_rec['name']  = $user->forename." ".$user->surname;
   		$user_rec['email'] = $user->email;
		
		if($user->forename == "")
        {
            $this->HandleError("There was an error updating your account. Please contact support");
            return false;
        }	 
		
		try
		{ 
			// this function creates all of the records and performs rollback and email if error  
			if(!$this->createUserRecords($user->email, $user->confirmcode)){
				$this->HandleError("There was an error creating your account. If the problem continues please contact support");
				return false;
			}
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($user_rec));
			return false;
					
		}
		
        return true;
    }
	

		
    function ChangePasswordInDB($user_rec, $newpwd)
    {
		
		$user = new Login();	
				
		if(!$user->get_user_for_field_with_value("email", $user_rec['email']))
	    {
	        $this->HandleError("ERROR - could not find account");
	        return false;
	    }
		
		$passwordUpdate = new Password();
		
		if(!$passwordUpdate->updatePasswordForUser($newpwd, $user)){
			
	        $this->HandleError("ERROR - there was an error updating your password. Please contact support.");
	        return false;
		}
		
		return true;
		
    }
	
    
    function GetUserFromEmail($email,&$user_rec)
    {
		
		$user = new Login();			

		if(!$user->get_user_for_field_with_value("email", $email))
	    {
            $this->HandleError("There is no user with email: $email");
            return false;
	    }
		
		$retID = $user->id;
		$name = $user->forename ." ". $user->surname;
		$retEmail = $user->email;
		$retPassword = $user->password;
		$retConfirm = $user->confirmcode;
			
		$user_rec =	array(
			"id_user" => $retID,
			"name" => $name,	
			"email" => $retEmail,
			"password" => $retPassword,
			"confirmcode" => $retConfirm,
		);
		
        return true;
    }
	
	
    
    function SendUserWelcomeEmail(&$user_rec)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($user_rec['email'],$user_rec['name']);
        
        $mailer->Subject = "Welcome to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Welcome! Your registration  with ".$this->sitename." is completed.\r\n".
        "\r\n".
        "Regards,\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }
	
	
    
    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$user_rec['name']."\r\n".
        "Email address: ".$user_rec['email']."\r\n";
        
        if(!$mailer->Send())
        {
			$this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }
        
      
    
    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
        $validator->addValidation("forename","req","Please fill in Name");
		$validator->addValidation("surename","req","Please fill in Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");
  
        $validator->addValidation("password","req","Please fill in Password");

        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }
		
		// use php filters to validate email address
		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

		// Validate e-mail
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->HandleError("$email is a valid email address");
            return false;
		}
				      
        return true;
    }
    
	
	
    function CollectRegistrationSubmission(&$formvars)
    {
		$formvars['forename'] = $this->clean($_POST['forename']);
		$formvars['surename'] = $this->clean($_POST['surename']);
        $formvars['email'] = $this->clean($_POST['email']);
        $formvars['password'] = $this->clean($_POST['password']);
    }
	
	
    
    function SendUserConfirmationEmail(&$formvars)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
		
		$name =  $formvars['forename'] ." ". $formvars['surename'];
        
        $mailer->AddAddress($formvars['email'],$name);
        
        $mailer->Subject = "Your registration with ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $confirmcode = $formvars['confirmcode'];
        
        $confirm_url = $this->GetAbsoluteURLFolder().'/confirmreg.php?code='.$confirmcode;
        
        $mailer->Body ="Hello ".$name."\r\n\r\n".
        "Thanks for your registration with ".$this->sitename."\r\n".
        "Please click the link below to confirm your registration.\r\n".
        "$confirm_url\r\n".
        "\r\n".
        "Regards,\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }
	
	
	
	
    function GetAbsoluteURLFolder()
    {	
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }
	
	
    
    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
		
		$name =  $formvars['forename'] ." ". $formvars['surename'];
        
        $mailer->Subject = "New registration: ".$name;

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$name."\r\n".
        "Email address: ".$formvars['email'];
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
	
	
    function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }

       	try
		{ 
	        if(!$this->IsFieldUnique($formvars))
	        {
	            $this->HandleError("This email is already registered");
	            return false;
	        }
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from SaveToDatabase fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($user_rec));
					
		}
                      		
		try
		{ 
	        if(!$this->InsertIntoDB($formvars))
	        {
	            $this->HandleError("Inserting to Database failed!");
	            return false;
	        }
		}
		catch (Exception $e){
			
			$this->createErrorLog("The error has been triggered from SaveToDatabase fg_membersite.php : ".$e->getMessage(),E_ALL,$this->outputArray($formvars));
            $this->HandleError("Inserting to Database failed!");
            return false;
					
		}
		
        return true;
    }
	
	
	
    
    function IsFieldUnique($formvars)
    {
		
		$user = new Login();
				
		// returns true if a record is found	
		if($user->get_user_for_field_with_value("email", $formvars['email']))
	    {
	        return false;
	    }
		
        return true;
		
    }
    
	
	
    function DBLogin()
    {

        $this->connection = mysqli_connect($this->db_host,$this->username,$this->pwd,$this->database);
						
		if (mysqli_connect_errno()){
			//$this->emailMe("Error connecting to database", "The error is: ".mysqli_connect_errno());
            return false;
  		}

        if(!mysqli_query($this->connection, "SET NAMES 'UTF8'"))
        {
            return false;
        }
        return true;
    }  
    
	

	function InsertIntoDB(&$formvars)
    {
		
		$user = new Login();			

        $confirmcode = $this->MakeConfirmationMd5($formvars['email']);
        $formvars['confirmcode'] = $confirmcode;

		$user->forename = htmlentities($formvars['forename']);
		$user->surname = htmlentities($formvars['surename']);
		$user->email = $formvars['email'];
		$user->password = password_hash($formvars['password'], PASSWORD_DEFAULT);
		$user->resetpassword = NULL;
		$user->confirmcode = $confirmcode;
		$user->registrationStart = date("Y-m-d H:i:s");
		$user->membershipStart = NULL;

	    if(!$user->create())
	    {
	        $this->HandleError("There was an error creating your account. Please contact support");
	        return false;
	    }
		
		return true;
				
    }
	
	
	
	
	
    function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
	
	
	
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }
	

	
	
	//---------------------------------------------------
	// check the username and passord are valid for login
	//---------------------------------------------------
	function CheckLoginInDB($email,$password){
		
		$user = new Login();
		
		// make username / email lower case for easy login
		$email = strtolower($email);
							
	    if(!$user->get_user_for_field_with_value("email" , $email))
	    {
	        $this->HandleError("Error logging in. The username or password does not match");
	        return false;
	    }

		$storedName  = $user->forename ." ". $user->surname;
		$storedEmail = $user->email;
		$storedpassword = $user->password;
		$confirm = $user->confirmcode;
		
		$passwordVerify = new Password();
		if($passwordVerify->isPasswordValidForUser($password, $user))
		{
	          $_SESSION['name_of_user']  = $storedName;
	          $_SESSION['email_of_user'] = $storedEmail;
		  
			  if($confirm == "y"){
				  return true;
			  }else{
				  $_SESSION['confirmCode'] = $confirm;
				  $this->RedirectToURL("newRegEmail.php");
				  return false;
			  }
		}else
		{
			$this->HandleError("Error logging in. The username or password does not match");
			return false;
		}	
	}
	
	
	
	function createUserRecords($username, $confirmcode)
	{
		
		$user = new Login();
		
		$confirmcode = $this->clean($confirmcode);
							
	    if(!$user->get_user_for_field_with_value("confirmcode" , $confirmcode))
	    {
	        $this->HandleError("Error logging in. Your account could not be found");
	        return false;
	    }
		
		$user->confirmcode = 'y';
		$user->membershipStart = date("Y-m-d H:i:s");
		
	    if(!$user->save())
	    {
	        $this->HandleError("There was an error creating your account. Please contact support.");
	        return false;
	    }
		
		return true;
	}	
}
?>
