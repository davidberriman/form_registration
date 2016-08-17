<?php
# ========================================================================#
#
#  Class for interacting with the login database table 
# 
# ========================================================================#
require_once("database.php");
require_once("sanitize.php");
require_once("class.error_log.php");

class Login extends MySQLDatabase
{
	protected static $table_name="login";
	
	public $forename;
	public $surname;
	public $email; 
	public $password;	
	public $resetpassword;
	public $confirmcode;
	public $registrationStart;
	public $membershipStart;
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
	
	

	public function create(){
		
		$forename = $this->escape_value($this->forename);
		$surname = $this->escape_value($this->surname);
		$email = strtolower($this->escape_value($this->email));
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$password = $this->password;
		$resetpassword = $this->escape_value($this->resetpassword);
		$confirmcode = $this->escape_value($this->confirmcode);
		$registrationStart = $this->escape_value($this->registrationStart);
		$membershipStart = $this->escape_value($this->membershipStart);
		
		try
		{ 
			return $this->createUser($forename, $surname, $email, $password, $resetpassword, $confirmcode, $registrationStart, $membershipStart);
		}
		catch (Exception $e){
			
			$log = new ErrorLog();
			$log->createErrorLog($e);
			return false;		
		}
	}
	
		
	private function createUser($forename, $surname, $email, $password, $resetpassword, $confirmcode, $registrationStart, $membershipStart){
		
		global $database;
		
		$sql = "INSERT INTO ".self::$table_name." (forename, surname, email, password, resetpassword, confirmcode, registrationStart, membershipStart) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
				
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('ssssssss', $forename, $surname, $email, $password, $resetpassword, $confirmcode, $registrationStart, $membershipStart);
			
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (Login:createUser) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();
			if ($stmt->errno) {
				throw new Exception("Exception thrown (Login:createUser) Error logged as: ".$stmt->error ,E_ALL);
			}
						
			return (mysqli_affected_rows($database->get_connection()) == 1) ? true : false;
						
		}
		
		throw new Exception("ERROR - Querying login database table : ".$database->get_connection()->error,E_ALL);
			
		return false;
	}
	
	
	
	public function update(){
				
		$forename = $this->escape_value($this->forename);
		$surname = $this->escape_value($this->surname);
		$email = strtolower($this->escape_value($this->email));
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$password = $this->password;
		$resetpassword = $this->escape_value($this->resetpassword);
		$confirmcode = $this->escape_value($this->confirmcode);
		$registrationStart = $this->escape_value($this->registrationStart);
		$membershipStart = $this->escape_value($this->membershipStart);
		
		try
		{ 
			return $this->updateUser($forename, $surname, $email, $password, $resetpassword, $confirmcode, $registrationStart, $membershipStart);
		}
		catch (Exception $e){
			
			$log = new ErrorLog();
			$log->createErrorLog($e);
			return false;		
		}
	}
	
	
	
	private function updateUser($forename, $surname, $email, $password, $resetpassword, $confirmcode, $registrationStart, $membershipStart){
		
		global $database;
		
		$sql = "UPDATE ".self::$table_name." SET forename=?, surname=?, email=?, password=?, resetpassword=?, confirmcode=?, registrationStart=?, membershipStart=?
				WHERE email=? LIMIT 1 ";
		
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('sssssssss', $forename, $surname, $email, $password, $resetpassword, $confirmcode, $registrationStart, $membershipStart, $email);
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (Login:updateUser) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();
			if ($stmt->errno) {
				throw new Exception("Exception thrown (Login:updateUser) Error logged as: ".$stmt->error ,E_ALL);
			}
						
			return (mysqli_affected_rows($database->get_connection()) == 1) ? true : false;
						
		}
		
		throw new Exception("ERROR - Querying login database table : ".$database->get_connection()->error,E_ALL);
		
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
		
		$id = $this->id;
		
		if(!is_numeric($id))
		{
			return false;
		}
						
		$sql = "DELETE FROM ".self::$table_name." WHERE id=? LIMIT 1 ";
		
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('s', $id);
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (Login:deleteComment) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();
			if ($stmt->errno) {
				throw new Exception("Exception thrown (Login:deleteComment) Error logged as: ".$stmt->error ,E_ALL);
			}
						
			return (mysqli_affected_rows($database->get_connection()) == 1) ? true : false;
						
		}
		
		throw new Exception("ERROR - Querying login database table : ".$database->get_connection()->error,E_ALL);
		
		return false;
	}
	
	
	// --------------------------------------------------------------------------
	// This could be called using the confirmcode / email as the retrieve profile
	// --------------------------------------------------------------------------
	public function get_user_for_field_with_value($field, $value){
		
		global $database;
		
		$sanitize = new Sanitize();
		$cleanValue = $sanitize->clean($value);
		
		if(!isset($cleanValue))
		{
			return false;
		}
						
		$sql  = "SELECT id, forename, surname, email, password, resetpassword, confirmcode, registrationStart, membershipStart FROM ". self::$table_name ." WHERE {$field} = ?  ";
		
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
	
		    $stmt->bind_result($id, $forename, $surname, $email, $password, $resetpassword, $confirmcode, $registrationStart, $membershipStart);
			
			$num_of_rows = $stmt->num_rows;
						
			$stmt->fetch();
			
			$this->id = $id;
			$this->forename = $forename;
			$this->surname = $surname;
			$this->email = $email;
			$this->password = $password;
			$this->resetpassword = $resetpassword;
			$this->confirmcode = $confirmcode;
			$this->registrationStart = $registrationStart;
			$this->membershipStart = $membershipStart;
						
			return ($num_of_rows == 1) ? true : false;
						
		}
		
		throw new Exception("ERROR - Querying login database table : ".$database->get_connection()->error,E_ALL);
					
		return false;
	}
	
	
	
	
	// --------------------------------------------------------------------------
	// This could be called using the confirmcode / email as the retrieve profile
	// --------------------------------------------------------------------------
	public function get_user_for_field_and_field_with_value($field, $value, $field2, $value2){
		
		global $database;
		
		$sanitize = new Sanitize();
		$cleanValue = $sanitize->clean($value);
		$cleanValue2 = $sanitize->clean($value2);
		
		if(!isset($cleanValue) || !isset($cleanValue2))
		{
			return false;
		}
						
		$sql  = "SELECT id, forename, surname, email, password, resetpassword, confirmcode, registrationStart, membershipStart FROM ". self::$table_name ." WHERE {$field} = ? AND {$field2} = ? ";		
		
		if ($stmt = $database->get_connection()->prepare($sql)) {
			
			$stmt->bind_param('ss', $cleanValue, $cleanValue2);
			if ( false===$stmt) {
			 	throw new Exception("Exception thrown (Login:get_user_for_field_and_field_with_value) Error logged as: ".$stmt->error ,E_ALL);
			}
			
			$stmt->execute();
			if ($stmt->errno) {
				throw new Exception("Exception thrown (Login:get_user_for_field_and_field_with_value) Error logged as: ".$stmt->error ,E_ALL);
			}

		    $stmt->store_result();
	
		    $stmt->bind_result($id, $forename, $surname, $email, $password, $resetpassword, $confirmcode, $registrationStart, $membershipStart);
			
			$num_of_rows = $stmt->num_rows;
						
			$stmt->fetch();
			
			$this->id = $id;
			$this->forename = $forename;
			$this->surname = $surname;
			$this->email = $email;
			$this->password = $password;
			$this->resetpassword = $resetpassword;
			$this->confirmcode = $confirmcode;
			$this->registrationStart = $registrationStart;
			$this->membershipStart = $membershipStart;
						
			return ($num_of_rows == 1) ? true : false;
						
		}
		
		throw new Exception("ERROR - Querying login database table : ".$database->get_connection()->error,E_ALL);
					
		return false;
	}
		
}

?>