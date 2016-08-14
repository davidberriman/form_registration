<?php
# ========================================================================#
#
#  Keep a lof of all errors in either a log file / table which is specified
#  in the initialize.php form
# 
# ========================================================================#
require_once("database.php");
require_once("sanitize.php");

class ErrorLog extends MySQLDatabase
{
	protected static $table_name="errorLogs";
	
	public $email;
	public $errorlog;
	public $errornumber;
	public $errorDateTime;
	public $id;
	
	public $errors = array();
	
	
	public function save(){	
			
		if(isset($this->id))
		{
			return $this->update();
		}else
		{
			return $this->create();
		}
	}
	
	
	public function createErrorLog($error)
	{
		if(!isSet($error) || $error == "")
		{
			return;
		}
		
		if(ERROR_LOG_TYPE == "email")
		{			
			if($this->emailError($error))
			{
				return true;
			}
			
			return false;
		}	
		
		if(ERROR_LOG_TYPE == "file")
		{
			$this->logError($error);
			return;
		}
		
		if(ERROR_LOG_TYPE == "db")
		{
			$this->saveError($error);
		}
		
	}
	
	
	private function logError($error)
	{
		$error = $error.PHP_EOL;
		
		if(null !== ERROR_LOG_DIR && file_exists(ERROR_LOG_DIR))
		{
			error_log($error, 3, ERROR_LOG_DIR, "");
			return;
		}
				
		error_log($error,0, "", "");
	}
	
	
	private function saveError($error)
	{
		// save error message to database

    	$this->email = $user;
	    $this->errorlog = $error;
		if($log->create())
		{
			return true;
		}
		
		return false;
	}
	
	
	private function emailError($error)
	{
        $mailer = new PHPMailer();
    
        $mailer->CharSet = 'utf-8';
    
        $mailer->AddAddress(ADMIN_EMAIL, ADMIN_NAME);
    
        $mailer->Subject = "Error log";

        $mailer->From = WEBSITE_EMAIL;        

        $mailer->Body = $error;

        if(!$mailer->Send())
        {
            return false;
        }
		
		return true;
	}
	

	public function create(){
		
		$email = $this->escape_value($this->email);
		$errorlog = $this->escape_value($this->errorlog);
		$errornumber = $this->escape_value($this->errornumber);
		$errorDateTime = $this->escape_value($this->errorDateTime);
		
		try
		{ 
			return $this->createUser($email, $errorlog, $errornumber, $errorDateTime);
		}
		catch (Exception $e){
			
			return false;		
		}
	}
	
		
	private function createUser($email, $errorlog, $errornumber, $errorDateTime){
		
		global $database;
		
		$sql = "INSERT INTO ".self::$table_name." (email, errorlog, errornumber, errorDateTime) 
				VALUES (?, ?, ?, ?) ";
				
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('ssss', $email, $errorlog, $errornumber, $errorDateTime);
			
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (ErrorLog:createUser) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();
			if ($stmt->errno) {
				throw new Exception("Exception thrown (ErrorLog:createUser) Error logged as: ".$stmt->error ,E_ALL);
			}
						
			return (mysqli_affected_rows($database->get_connection()) == 1) ? true : false;
						
		}
				
		return false;
	}	

		
}

?>