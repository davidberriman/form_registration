<?php
# ========================================================================#
#
#  Record login fails so user can be throttled in brute force attacks
# 
# ========================================================================#
require_once("database.php");
require_once("sanitize.php");
require_once("class.error_log.php");

class LoginFails extends MySQLDatabase
{
	protected static $table_name="failed_logins";
	
	public $username;
	public $login_count;
	public $last_time;
	
	public $errors = array();
	
	
	public function save(){	
			
		if(isset($this->username))
		{
			return $this->update();
		}else
		{
			return $this->create();
		}
	}
	

	public function create(){
		
		$username = $this->escape_value($this->username);
		$username = filter_var($username, FILTER_SANITIZE_EMAIL);
		$login_count = $this->escape_value($this->login_count);
		$last_time = $this->escape_value($this->last_time);
		$address = $_SERVER['REMOTE_ADDR'];
		
		try
		{ 
			return $this->createUser($username, $login_count, $last_time, $address);
		}
		catch (Exception $e){
			
			$log = new ErrorLog();
			$log->createErrorLog($e);
			return false;		
		}
	}
	
		
	private function createUser($username, $login_count, $last_time, $address){
		
		global $database;
		
		$sql = "INSERT INTO ".self::$table_name." (username, login_count, last_time, address) 
				VALUES (?, ?, ?, ?) ";
				
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('ssss', $username, $login_count, $last_time, $address);
			
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (LoginFails:createUser) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();
			if ($stmt->errno) {
				throw new Exception("Exception thrown (LoginFails:createUser) Error logged as: ".$stmt->error ,E_ALL);
			}
						
			return (mysqli_affected_rows($database->get_connection()) == 1) ? true : false;
						
		}
		
		throw new Exception("ERROR - Querying failed_logins database table : ".$database->get_connection()->error,E_ALL);
			
		return false;
	}
	
	
	
	public function update(){
				
		$username = $this->escape_value($this->username);
		$username = filter_var($username, FILTER_SANITIZE_EMAIL);
		$login_count = $this->escape_value($this->login_count);
		$last_time = $this->escape_value($this->last_time);
		$address = $_SERVER['REMOTE_ADDR'];
		
		try
		{ 
			return $this->updateUser($username, $login_count, $last_time, $address);
		}
		catch (Exception $e){
			
			$log = new ErrorLog();
			$log->createErrorLog($e);
			return false;		
		}
	}
	
	
	
	private function updateUser($username, $login_count, $last_time, $address){
		
		global $database;
		
		$sql = "UPDATE ".self::$table_name." SET username=?, login_count=?, last_time=?, address=?
				WHERE username=? LIMIT 1 ";
				
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('sssss', $username, $login_count, $last_time, $address, $username);
						
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (LoginFails:updateUser) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();			
			if ($stmt->errno) {
				throw new Exception("Exception thrown (LoginFails:updateUser) Error logged as: ".$stmt->error ,E_ALL);
			}
									
			return (mysqli_affected_rows($database->get_connection()) == 1) ? true : false;
		}
		
		throw new Exception("ERROR - Querying failed_logins database table : ".$database->get_connection()->error,E_ALL);
		
		return false;
	}
	
	
		
	public function delete(){
		
		try
		{ 
			return $this->deleteUser() ? true : false;
		}
		catch (Exception $e){
			
			$log = new ErrorLog();
			$log->createErrorLog($e);
			return false;		
		}
	}
	
	
	
	private function deleteUser(){
		
		global $database;
				
		$sanitize = new Sanitize();
		$cleanValue = $sanitize->clean($this->username);
			
		$sql = "DELETE FROM ".self::$table_name." WHERE username=? LIMIT 1 ";
		
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('s', $cleanValue);
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (LoginFails:deleteUser) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();
			if ($stmt->errno) {
				throw new Exception("Exception thrown (LoginFails:deleteUser) Error logged as: ".$stmt->error ,E_ALL);
			}
						
			return (mysqli_affected_rows($database->get_connection()) == 1) ? true : false;
						
		}
		
		throw new Exception("ERROR - Querying failed_logins database table : ".$database->get_connection()->error,E_ALL);
		
		return false;
	}
	
	
	// --------------------------------------------------------------------------
	// This could be called using the confirmcode / email as the retrieve profile
	// --------------------------------------------------------------------------
	public function get_user_for_field_with_value($field, $value){
		
		global $database;
						
		$sql  = "SELECT username, login_count, last_time, address FROM ". self::$table_name ." WHERE {$field} = ?  ";

		$sanitize = new Sanitize();
		$cleanValue = $sanitize->clean($value);
		
		if(!isset($cleanValue))
		{
			return false;
		}
		
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('s', $cleanValue);
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (Login:get_user_for_field_with_value) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();
			if ($stmt->errno) {
				throw new Exception("Exception thrown (Login:get_user_for_field_with_value) Error logged as: ".$stmt->error ,E_ALL);
			}

		    $stmt->store_result();
	
		    $stmt->bind_result($username, $login_count, $last_time, $address);
			
			$num_of_rows = $stmt->num_rows;
						
			$stmt->fetch();
			
			$this->username = $username;
			$this->login_count = $login_count;
			$this->last_time = $last_time;
						
			return ($num_of_rows == 1) ? true : false;
						
		}
		
		throw new Exception("ERROR - Querying failed_logins database table : ".$database->get_connection()->error,E_ALL);
					
		return false;
	}
	
	
		
}

?>